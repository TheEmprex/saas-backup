import React, { useState } from "react";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
  DialogClose,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { ScrollArea } from "@/components/ui/scroll-area";
import { FolderOpen } from "lucide-react";

export default function MoveToFolderModal({ isOpen, onClose, folders, onMoveToFolder }) {
  const [selectedFolderId, setSelectedFolderId] = useState(null);

  const handleMove = () => {
    if (selectedFolderId !== null) {
      onMoveToFolder(selectedFolderId);
    }
  };

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="sm:max-w-md">
        <DialogHeader>
          <DialogTitle>Move to Folder</DialogTitle>
        </DialogHeader>
        <ScrollArea className="h-64 border rounded-md my-4">
          <div className="p-2">
            <div 
              className={`flex items-center gap-3 p-2 rounded-md hover:bg-gray-100 cursor-pointer ${selectedFolderId === null ? 'bg-blue-50' : ''}`}
              onClick={() => setSelectedFolderId(null)}
            >
              <FolderOpen className="w-4 h-4" />
              <p className="font-medium">All (Remove from folder)</p>
            </div>
            {folders.map(folder => (
              <div 
                key={folder.id} 
                className={`flex items-center gap-3 p-2 rounded-md hover:bg-gray-100 cursor-pointer ${selectedFolderId === folder.id ? 'bg-blue-50' : ''}`}
                onClick={() => setSelectedFolderId(folder.id)}
              >
                <FolderOpen className="w-4 h-4" />
                <p className="font-medium">{folder.name}</p>
              </div>
            ))}
          </div>
        </ScrollArea>
        <DialogFooter>
          <DialogClose asChild>
            <Button type="button" variant="secondary">
              Cancel
            </Button>
          </DialogClose>
          <Button onClick={handleMove} disabled={selectedFolderId === null}>
            Move Conversation
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}