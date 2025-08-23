import axios from 'axios';

const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  }
});

// Add CSRF token to requests
api.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Message API
export const Message = {
  async list(orderBy = 'created_at') {
    const response = await api.get('/messages', { params: { orderBy } });
    return response.data;
  },

  async filter(filters, orderBy = 'created_at') {
    const response = await api.get('/messages', { params: { ...filters, orderBy } });
    return response.data;
  },

  async create(data) {
    const response = await api.post('/messages', data);
    return response.data;
  },

  async update(id, data) {
    const response = await api.put(`/messages/${id}`, data);
    return response.data;
  },

  async delete(id) {
    const response = await api.delete(`/messages/${id}`);
    return response.data;
  }
};

// Conversation API
export const Conversation = {
  async list(orderBy = 'updated_at') {
    const response = await api.get('/conversations', { params: { orderBy } });
    return response.data;
  },

  async create(data) {
    const response = await api.post('/conversations', data);
    return response.data;
  },

  async update(id, data) {
    const response = await api.put(`/conversations/${id}`, data);
    return response.data;
  },

  async delete(id) {
    const response = await api.delete(`/conversations/${id}`);
    return response.data;
  }
};

// Contact API  
export const Contact = {
  async list(orderBy = 'name') {
    const response = await api.get('/contacts', { params: { orderBy } });
    return response.data;
  },

  async create(data) {
    const response = await api.post('/contacts', data);
    return response.data;
  },

  async update(id, data) {
    const response = await api.put(`/contacts/${id}`, data);
    return response.data;
  },

  async delete(id) {
    const response = await api.delete(`/contacts/${id}`);
    return response.data;
  }
};

// Folder API
export const Folder = {
  async list(orderBy = 'order_index') {
    const response = await api.get('/message-folders', { params: { orderBy } });
    return response.data;
  },

  async create(data) {
    const response = await api.post('/message-folders', data);
    return response.data;
  },

  async update(id, data) {
    const response = await api.put(`/message-folders/${id}`, data);
    return response.data;
  },

  async delete(id) {
    const response = await api.delete(`/message-folders/${id}`);
    return response.data;
  }
};

// File upload function
export const UploadFile = async ({ file }) => {
  const formData = new FormData();
  formData.append('file', file);
  
  const response = await api.post('/upload', formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    }
  });
  
  return response.data;
};

export default {
  Message,
  Conversation,
  Contact,  
  Folder,
  UploadFile
};
