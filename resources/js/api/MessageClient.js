// MessageClient.js
import axios from 'axios';

export const sendMessage = async (userId, content, attachments = [], jobPostId = null) => {
  try {
    const formData = new FormData();
    formData.append('content', content);
    attachments.forEach((file, idx) => formData.append(`attachments[${idx}]`, file));
    if (jobPostId) {
      formData.append('job_post_id', jobPostId);
    }

    const { data } = await axios.post(`/messages/${userId}`, formData);
    return data.message;
  } catch (error) {
    console.error('Error sending message:', error);
    throw error;
  }
};

export const fetchMessages = async (contactId, lastId = null) => {
  try {
    const response = await axios.get(`/messages/${contactId}`, {
      params: { last_id: lastId },
    });
    return response.data.messages;
  } catch (error) {
    console.error('Error fetching messages:', error);
    throw error;
  }
};

export const markMessageAsRead = async (messageId) => {
  try {
    await axios.put(`/messages/${messageId}/read`);
  } catch (error) {
    console.error('Error marking message as read:', error);
    throw error;
  }
};

