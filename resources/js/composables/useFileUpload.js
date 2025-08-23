import { ref, computed, onMounted, onUnmounted } from 'vue'

export function useFileUpload(options = {}) {
    // Default options
    const defaultOptions = {
        accept: '*/*', // File types to accept
        multiple: true, // Allow multiple files
        maxFiles: 10, // Maximum number of files
        maxSize: 10 * 1024 * 1024, // 10MB in bytes
        autoUpload: false, // Automatically upload when files are added
        uploadUrl: '/api/upload', // Upload endpoint
        uploadMethod: 'POST', // HTTP method
        headers: {}, // Additional headers
        onProgress: null, // Progress callback
        onComplete: null, // Complete callback
        onError: null, // Error callback
    }
    
    const config = { ...defaultOptions, ...options }
    
    // State
    const isDragOver = ref(false)
    const isUploading = ref(false)
    const files = ref([])
    const uploadProgress = ref({})
    const errors = ref([])
    const completedUploads = ref([])
    
    // Computed
    const hasFiles = computed(() => files.value.length > 0)
    const canAddMore = computed(() => files.value.length < config.maxFiles)
    const totalSize = computed(() => 
        files.value.reduce((total, file) => total + (file.file?.size || 0), 0)
    )
    const totalSizeFormatted = computed(() => formatFileSize(totalSize.value))
    
    // File validation
    const validateFile = (file) => {
        const errors = []
        
        // Check file size
        if (file.size > config.maxSize) {
            errors.push(`File too large. Maximum size is ${formatFileSize(config.maxSize)}`)
        }
        
        // Check file type
        if (config.accept !== '*/*') {
            const acceptedTypes = config.accept.split(',').map(type => type.trim())
            const isAccepted = acceptedTypes.some(type => {
                if (type.startsWith('.')) {
                    // Extension check
                    return file.name.toLowerCase().endsWith(type.toLowerCase())
                } else if (type.includes('/')) {
                    // MIME type check
                    return file.type.match(type.replace('*', '.*'))
                }
                return false
            })
            
            if (!isAccepted) {
                errors.push(`File type not accepted. Accepted types: ${config.accept}`)
            }
        }
        
        return errors
    }
    
    // Create file info object
    const createFileInfo = (file) => {
        const id = Date.now() + Math.random()
        const validationErrors = validateFile(file)
        
        return {
            id,
            file,
            name: file.name,
            size: file.size,
            sizeFormatted: formatFileSize(file.size),
            type: file.type,
            lastModified: file.lastModified,
            preview: null, // Will be set for images
            status: validationErrors.length > 0 ? 'invalid' : 'pending', // pending, uploading, completed, failed, invalid
            progress: 0,
            errors: validationErrors,
            uploadResponse: null
        }
    }
    
    // Generate preview for images
    const generatePreview = async (fileInfo) => {
        if (!fileInfo.file.type.startsWith('image/')) return
        
        try {
            const url = URL.createObjectURL(fileInfo.file)
            fileInfo.preview = url
        } catch (error) {
            console.warn('Failed to generate preview:', error)
        }
    }
    
    // Add files
    const addFiles = async (fileList) => {
        const newFiles = Array.from(fileList)
        
        // Check if we can add more files
        if (files.value.length + newFiles.length > config.maxFiles) {
            const error = `Cannot add ${newFiles.length} files. Maximum is ${config.maxFiles} files total.`
            errors.value.push(error)
            return false
        }
        
        // Create file info objects
        const fileInfos = newFiles.map(createFileInfo)
        
        // Generate previews for images
        await Promise.all(fileInfos.map(generatePreview))
        
        // Add to files array
        files.value.push(...fileInfos)
        
        // Auto upload if enabled
        if (config.autoUpload) {
            const validFiles = fileInfos.filter(f => f.status !== 'invalid')
            if (validFiles.length > 0) {
                uploadFiles(validFiles.map(f => f.id))
            }
        }
        
        return true
    }
    
    // Remove file
    const removeFile = (fileId) => {
        const index = files.value.findIndex(f => f.id === fileId)
        if (index > -1) {
            const fileInfo = files.value[index]
            
            // Revoke preview URL to prevent memory leaks
            if (fileInfo.preview) {
                URL.revokeObjectURL(fileInfo.preview)
            }
            
            files.value.splice(index, 1)
            
            // Remove from upload progress
            delete uploadProgress.value[fileId]
        }
    }
    
    // Clear all files
    const clearFiles = () => {
        // Revoke all preview URLs
        files.value.forEach(fileInfo => {
            if (fileInfo.preview) {
                URL.revokeObjectURL(fileInfo.preview)
            }
        })
        
        files.value = []
        uploadProgress.value = {}
        errors.value = []
    }
    
    // Upload files
    const uploadFiles = async (fileIds = null) => {
        const filesToUpload = fileIds 
            ? files.value.filter(f => fileIds.includes(f.id) && f.status === 'pending')
            : files.value.filter(f => f.status === 'pending')
        
        if (filesToUpload.length === 0) return
        
        isUploading.value = true
        
        try {
            // Upload all files in a single request (batch upload)
            await uploadBatch(filesToUpload)
        } finally {
            isUploading.value = false
        }
    }
    
    // Upload batch of files
    const uploadBatch = async (filesToUpload) => {
        // Set all files to uploading status
        filesToUpload.forEach(fileInfo => {
            fileInfo.status = 'uploading'
            fileInfo.progress = 0
        })
        
        const formData = new FormData()
        
        // Add all files to the same FormData
        filesToUpload.forEach((fileInfo, index) => {
            formData.append('files[]', fileInfo.file)
        })
        
        // Add additional form data if needed
        if (options.formData) {
            Object.entries(options.formData).forEach(([key, value]) => {
                formData.append(key, value)
            })
        }
        
        try {
            const response = await fetch(config.uploadUrl, {
                method: config.uploadMethod,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    ...config.headers
                },
                body: formData,
            })
            
            if (!response.ok) {
                throw new Error(`Upload failed: ${response.statusText}`)
            }
            
            const result = await response.json()
            
            // Update all files status to completed
            filesToUpload.forEach(fileInfo => {
                fileInfo.status = 'completed'
                fileInfo.progress = 100
                fileInfo.uploadResponse = result
                
                completedUploads.value.push(fileInfo)
                
                if (config.onComplete) {
                    config.onComplete(fileInfo, result)
                }
            })
            
        } catch (error) {
            // Update all files status to failed
            filesToUpload.forEach(fileInfo => {
                fileInfo.status = 'failed'
                fileInfo.errors.push(error.message)
                
                if (config.onError) {
                    config.onError(fileInfo, error)
                }
            })
        }
    }
    
    // Upload single file
    const uploadSingleFile = async (fileInfo) => {
        fileInfo.status = 'uploading'
        fileInfo.progress = 0
        
        const formData = new FormData()
        formData.append('file', fileInfo.file)
        
        // Add additional form data if needed
        if (options.formData) {
            Object.entries(options.formData).forEach(([key, value]) => {
                formData.append(key, value)
            })
        }
        
        try {
            const response = await fetch(config.uploadUrl, {
                method: config.uploadMethod,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    ...config.headers
                },
                body: formData,
            })
            
            if (!response.ok) {
                throw new Error(`Upload failed: ${response.statusText}`)
            }
            
            const result = await response.json()
            
            fileInfo.status = 'completed'
            fileInfo.progress = 100
            fileInfo.uploadResponse = result
            
            completedUploads.value.push(fileInfo)
            
            if (config.onComplete) {
                config.onComplete(fileInfo, result)
            }
            
        } catch (error) {
            fileInfo.status = 'failed'
            fileInfo.errors.push(error.message)
            
            if (config.onError) {
                config.onError(fileInfo, error)
            }
        }
    }
    
    // Drag and drop handlers
    const handleDragEnter = (e) => {
        e.preventDefault()
        e.stopPropagation()
        isDragOver.value = true
    }
    
    const handleDragLeave = (e) => {
        e.preventDefault()
        e.stopPropagation()
        
        // Only set to false if we're leaving the drop zone entirely
        if (!e.currentTarget.contains(e.relatedTarget)) {
            isDragOver.value = false
        }
    }
    
    const handleDragOver = (e) => {
        e.preventDefault()
        e.stopPropagation()
        
        // Show appropriate cursor
        e.dataTransfer.dropEffect = canAddMore.value ? 'copy' : 'none'
    }
    
    const handleDrop = async (e) => {
        e.preventDefault()
        e.stopPropagation()
        isDragOver.value = false
        
        const { files: droppedFiles } = e.dataTransfer
        
        if (droppedFiles.length > 0 && canAddMore.value) {
            await addFiles(droppedFiles)
        }
    }
    
    // File input handler
    const handleFileInput = async (e) => {
        const { files: selectedFiles } = e.target
        
        if (selectedFiles.length > 0) {
            await addFiles(selectedFiles)
        }
        
        // Clear the input so the same files can be selected again
        e.target.value = ''
    }
    
    // Utility functions
    const formatFileSize = (bytes) => {
        if (bytes === 0) return '0 Bytes'
        
        const k = 1024
        const sizes = ['Bytes', 'KB', 'MB', 'GB']
        const i = Math.floor(Math.log(bytes) / Math.log(k))
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
    }
    
    const getFileIcon = (fileType) => {
        if (fileType.startsWith('image/')) return '🖼️'
        if (fileType.startsWith('video/')) return '🎥'
        if (fileType.startsWith('audio/')) return '🎵'
        if (fileType.includes('pdf')) return '📄'
        if (fileType.includes('word')) return '📝'
        if (fileType.includes('excel') || fileType.includes('spreadsheet')) return '📊'
        if (fileType.includes('powerpoint') || fileType.includes('presentation')) return '📽️'
        if (fileType.includes('zip') || fileType.includes('rar')) return '📦'
        return '📎'
    }
    
    // Retry upload
    const retryUpload = (fileId) => {
        const fileInfo = files.value.find(f => f.id === fileId)
        if (fileInfo && fileInfo.status === 'failed') {
            fileInfo.errors = []
            fileInfo.status = 'pending'
            uploadSingleFile(fileInfo)
        }
    }
    
    // Get files by status
    const getFilesByStatus = (status) => {
        return files.value.filter(f => f.status === status)
    }
    
    // Cleanup function
    const cleanup = () => {
        clearFiles()
    }
    
    // Setup cleanup on unmount
    onUnmounted(() => {
        cleanup()
    })
    
    return {
        // State
        isDragOver,
        isUploading,
        files,
        uploadProgress,
        errors,
        completedUploads,
        
        // Computed
        hasFiles,
        canAddMore,
        totalSize,
        totalSizeFormatted,
        
        // Methods
        addFiles,
        removeFile,
        clearFiles,
        uploadFiles,
        retryUpload,
        
        // Event handlers
        handleDragEnter,
        handleDragLeave,
        handleDragOver,
        handleDrop,
        handleFileInput,
        
        // Utilities
        formatFileSize,
        getFileIcon,
        getFilesByStatus,
        
        // Config
        config,
        
        // Cleanup
        cleanup
    }
}
