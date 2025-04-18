import axios from 'axios';

const api = axios.create({
  baseURL: 'http://host.docker.internal:8000/api',
  timeout: 10000,
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('jwt_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
}, (error) => {
  return Promise.reject(error);
});

api.interceptors.response.use((response) => {
  return response;
}, (error) => {
  if (error.response && error.response.status === 401) {
    window.location.href = '/login';
  }
  return Promise.reject(error);
});

export default api;
