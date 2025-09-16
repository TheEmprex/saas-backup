import { defineStore } from 'pinia'
// Use global axios instance configured in messaging-app.js

export const useMessageFoldersStore = defineStore('messageFolders', {
  state: () => ({
    folders: [],
    loading: false,
    error: null
  }),

  getters: {
    sortedFolders: (state) => {
      return [...state.folders].sort((a, b) => a.sort_order - b.sort_order)
    },
    
    getFolderById: (state) => (id) => {
      return state.folders.find(folder => folder.id === id)
    }
  },

  actions: {
    async fetchFolders() {
      this.loading = true
      try {
const response = await window.axios.get('/messages/api/message-folders')
        this.folders = response.data.folders
        this.error = null
      } catch (err) {
        this.error = 'Failed to load folders'
        console.error('Error fetching folders:', err)
      } finally {
        this.loading = false
      }
    },

    async createFolder(folderData) {
      try {
const response = await window.axios.post('/messages/api/message-folders', folderData)
        this.folders.push(response.data.folder)
        this.error = null
        return response.data.folder
      } catch (err) {
        this.error = 'Failed to create folder'
        console.error('Error creating folder:', err)
        throw err
      }
    },

    async updateFolder(folderId, updates) {
      try {
const response = await window.axios.put(`/messages/api/message-folders/${folderId}`, updates)
        const index = this.folders.findIndex(f => f.id === folderId)
        if (index !== -1) {
          this.folders[index] = response.data.folder
        }
        this.error = null
        return response.data.folder
      } catch (err) {
        this.error = 'Failed to update folder'
        console.error('Error updating folder:', err)
        throw err
      }
    },

    async deleteFolder(folderId) {
      try {
await window.axios.delete(`/messages/api/message-folders/${folderId}`)
        this.folders = this.folders.filter(f => f.id !== folderId)
        this.error = null
      } catch (err) {
        this.error = 'Failed to delete folder'
        console.error('Error deleting folder:', err)
        throw err
      }
    },

    async addConversationToFolder(folderId, conversationId) {
      try {
await window.axios.post(`/messages/api/message-folders/${folderId}/conversations`, {
          conversation_id: conversationId
        })
        await this.fetchFolders() // Refresh folders to get updated conversation lists
        this.error = null
      } catch (err) {
        this.error = 'Failed to add conversation to folder'
        console.error('Error adding conversation to folder:', err)
        throw err
      }
    },

    async removeConversationFromFolder(folderId, conversationId) {
      try {
await window.axios.delete(`/messages/api/message-folders/${folderId}/conversations/${conversationId}`)
        await this.fetchFolders() // Refresh folders to get updated conversation lists
        this.error = null
      } catch (err) {
        this.error = 'Failed to remove conversation from folder'
        console.error('Error removing conversation from folder:', err)
        throw err
      }
    },

    async reorderFolders(folderIds) {
      try {
await window.axios.post('/messages/api/message-folders/reorder', { folders: folderIds })
        await this.fetchFolders() // Refresh folders to get updated order
        this.error = null
      } catch (err) {
        this.error = 'Failed to reorder folders'
        console.error('Error reordering folders:', err)
        throw err
      }
    }
  }
})
