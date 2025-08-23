
import React, { useState, useEffect } from "react";
import { Folder, Conversation } from "@/entities/all";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { 
  Plus, 
  FolderOpen, 
  MoreVertical, 
  Edit3, 
  Trash2,
  Move,
  MessageSquare
} from "lucide-react";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,  
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Label } from "@/components/ui/label";
import { DragDropContext, Droppable, Draggable } from "@hello-pangea/dnd";

const FOLDER_COLORS = [
  { name: 'blue', class: 'bg-blue-100 text-blue-800 border-blue-200' },
  { name: 'green', class: 'bg-green-100 text-green-800 border-green-200' },
  { name: 'purple', class: 'bg-purple-100 text-purple-800 border-purple-200' },
  { name: 'orange', class: 'bg-orange-100 text-orange-800 border-orange-200' },
  { name: 'red', class: 'bg-red-100 text-red-800 border-red-200' },
  { name: 'gray', class: 'bg-gray-100 text-gray-800 border-gray-200' }
];

const FOLDER_ICONS = [
  'folder', 'briefcase', 'users', 'star', 'heart', 'archive'
];

export default function FoldersPage() {
  const [folders, setFolders] = useState([]);
  const [conversations, setConversations] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [showCreateDialog, setShowCreateDialog] = useState(false);
  const [editingFolder, setEditingFolder] = useState(null);
  const [newFolder, setNewFolder] = useState({
    name: '',
    color: 'blue',
    icon: 'folder'
  });

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    setIsLoading(true);
    try {
      const [foldersData, conversationsData] = await Promise.all([
        Folder.list("order_index"),
        Conversation.list("-last_message_time")
      ]);
      
      setFolders(foldersData);
      setConversations(conversationsData);
    } catch (error) {
      console.error("Error loading data:", error);
    }
    setIsLoading(false);
  };

  const getConversationCount = (folderId) => {
    return conversations.filter(conv => conv.folder_id === folderId).length;
  };

  const handleCreateFolder = async () => {
    if (!newFolder.name.trim()) return;

    try {
      await Folder.create({
        ...newFolder,
        order_index: folders.length
      });
      
      setNewFolder({ name: '', color: 'blue', icon: 'folder' });
      setShowCreateDialog(false);
      loadData();
    } catch (error) {
      console.error("Error creating folder:", error);
    }
  };

  const handleUpdateFolder = async () => {
    if (!editingFolder || !newFolder.name.trim()) return;

    try {
      await Folder.update(editingFolder.id, newFolder);
      setEditingFolder(null);
      setNewFolder({ name: '', color: 'blue', icon: 'folder' });
      loadData();
    } catch (error) {
      console.error("Error updating folder:", error);
    }
  };

  const handleDeleteFolder = async (folderId) => {
    try {
      // Move conversations out of folder before deleting
      const folderConversations = conversations.filter(conv => conv.folder_id === folderId);
      for (const conv of folderConversations) {
        await Conversation.update(conv.id, { folder_id: null });
      }
      
      await Folder.delete(folderId);
      loadData();
    } catch (error) {
      console.error("Error deleting folder:", error);
    }
  };

  const startEdit = (folder) => {
    setEditingFolder(folder);
    setNewFolder({
      name: folder.name,
      color: folder.color,
      icon: folder.icon
    });
    setShowCreateDialog(true);
  };

  const cancelEdit = () => {
    setEditingFolder(null);
    setNewFolder({ name: '', color: 'blue', icon: 'folder' });
    setShowCreateDialog(false);
  };

  const getColorClass = (colorName) => {
    return FOLDER_COLORS.find(c => c.name === colorName)?.class || FOLDER_COLORS[0].class;
  };

  const onDragEnd = async (result) => {
    if (!result.destination) {
      return;
    }

    const items = Array.from(folders);
    const [reorderedItem] = items.splice(result.source.index, 1);
    items.splice(result.destination.index, 0, reorderedItem);

    setFolders(items);

    // Update order_index in the backend
    try {
      for (let i = 0; i < items.length; i++) {
        // Only update if the order_index has actually changed
        if (items[i].order_index !== i) {
          await Folder.update(items[i].id, { order_index: i });
        }
      }
    } catch (error) {
      console.error("Failed to update folder order", error);
      // Optionally revert state on error by reloading data
      loadData();
    }
  };

  return (
    <div className="p-6 bg-gray-50 min-h-screen">
      <div className="max-w-7xl mx-auto">
        <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
          <div>
            <h1 className="text-3xl font-bold text-gray-900">Folders</h1>
            <p className="text-gray-600 mt-1">Organize your conversations into folders</p>
          </div>
          
          <Dialog open={showCreateDialog} onOpenChange={setShowCreateDialog}>
            <DialogTrigger asChild>
              <Button className="bg-blue-600 hover:bg-blue-700">
                <Plus className="w-4 h-4 mr-2" />
                Create Folder
              </Button>
            </DialogTrigger>
            <DialogContent className="sm:max-w-md">
              <DialogHeader>
                <DialogTitle>
                  {editingFolder ? 'Edit Folder' : 'Create New Folder'}
                </DialogTitle>
              </DialogHeader>
              <div className="space-y-4 pt-4">
                <div>
                  <Label htmlFor="folder-name">Folder Name</Label>
                  <Input
                    id="folder-name"
                    value={newFolder.name}
                    onChange={(e) => setNewFolder(prev => ({ ...prev, name: e.target.value }))}
                    placeholder="Enter folder name..."
                    className="mt-1"
                  />
                </div>
                
                <div>
                  <Label htmlFor="folder-color">Color</Label>
                  <Select 
                    value={newFolder.color} 
                    onValueChange={(value) => setNewFolder(prev => ({ ...prev, color: value }))}
                  >
                    <SelectTrigger className="mt-1">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      {FOLDER_COLORS.map((color) => (
                        <SelectItem key={color.name} value={color.name}>
                          <div className="flex items-center gap-2">
                            <div className={`w-3 h-3 rounded-full ${color.class}`}></div>
                            <span className="capitalize">{color.name}</span>
                          </div>
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                
                <div>
                  <Label htmlFor="folder-icon">Icon</Label>
                  <Select 
                    value={newFolder.icon} 
                    onValueChange={(value) => setNewFolder(prev => ({ ...prev, icon: value }))}
                  >
                    <SelectTrigger className="mt-1">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      {FOLDER_ICONS.map((icon) => (
                        <SelectItem key={icon} value={icon}>
                          <span className="capitalize">{icon}</span>
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                
                <div className="flex gap-3 pt-4">
                  <Button variant="outline" onClick={cancelEdit} className="flex-1">
                    Cancel
                  </Button>
                  <Button 
                    onClick={editingFolder ? handleUpdateFolder : handleCreateFolder}
                    className="flex-1 bg-blue-600 hover:bg-blue-700"
                  >
                    {editingFolder ? 'Update' : 'Create'}
                  </Button>
                </div>
              </div>
            </DialogContent>
          </Dialog>
        </div>

        <DragDropContext onDragEnd={onDragEnd}>
          <Droppable droppableId="folders" direction="horizontal">
            {(provided) => (
              <div
                {...provided.droppableProps}
                ref={provided.innerRef}
                className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
              >
                {folders.map((folder, index) => (
                  <Draggable key={folder.id} draggableId={folder.id} index={index}>
                    {(provided) => (
                      <div
                        ref={provided.innerRef}
                        {...provided.draggableProps}
                        {...provided.dragHandleProps}
                      >
                        <Card className="hover:shadow-md transition-shadow duration-200 bg-white">
                          <CardHeader className="pb-3 flex flex-row items-center justify-between">
                            <div className="flex items-center gap-3">
                              <div className={`p-2 rounded-lg ${getColorClass(folder.color)}`}>
                                <FolderOpen className="w-5 h-5" />
                              </div>
                              <CardTitle className="text-lg font-semibold">{folder.name}</CardTitle>
                            </div>
                            
                            <DropdownMenu>
                              <DropdownMenuTrigger asChild>
                                <Button variant="ghost" size="icon" className="text-gray-400 hover:text-gray-600">
                                  <MoreVertical className="w-4 h-4" />
                                </Button>
                              </DropdownMenuTrigger>
                              <DropdownMenuContent align="end">
                                <DropdownMenuItem onClick={() => startEdit(folder)}>
                                  <Edit3 className="w-4 h-4 mr-2" />
                                  Edit Folder
                                </DropdownMenuItem>
                                <DropdownMenuItem onClick={() => handleDeleteFolder(folder.id)}>
                                  <Trash2 className="w-4 h-4 mr-2" />
                                  Delete Folder
                                </DropdownMenuItem>
                              </DropdownMenuContent>
                            </DropdownMenu>
                          </CardHeader>
                          
                          <CardContent>
                            <div className="space-y-3">
                              <div className="flex items-center justify-between">
                                <span className="text-sm text-gray-600">Conversations</span>
                                <Badge variant="secondary" className={getColorClass(folder.color)}>
                                  {getConversationCount(folder.id)}
                                </Badge>
                              </div>
                              
                              <Button 
                                variant="outline" 
                                className="w-full"
                                onClick={() => {/* Navigate to messages with folder filter */}}
                              >
                                <MessageSquare className="w-4 h-4 mr-2" />
                                View Conversations
                              </Button>
                            </div>
                          </CardContent>
                        </Card>
                      </div>
                    )}
                  </Draggable>
                ))}
                {provided.placeholder}
              </div>
            )}
          </Droppable>
        </DragDropContext>

        {folders.length === 0 && !isLoading && (
          <div className="text-center py-12">
            <div className="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-lg flex items-center justify-center">
              <FolderOpen className="w-8 h-8 text-gray-400" />
            </div>
            <h3 className="text-lg font-medium text-gray-900 mb-2">No folders yet</h3>
            <p className="text-gray-500 mb-6">Create folders to organize your conversations</p>
            <Button 
              onClick={() => setShowCreateDialog(true)}
              className="bg-blue-600 hover:bg-blue-700"
            >
              <Plus className="w-4 h-4 mr-2" />
              Create Your First Folder
            </Button>
          </div>
        )}
      </div>
    </div>
  );
}
