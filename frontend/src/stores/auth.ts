import { defineStore } from 'pinia';
import api from '../services/axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('jwt_token') || '',
    user: null as null | { id: number; name: string; email: string },
  }),
  actions: {
    async login(email: string, password: string) {
      const response = await api.post('/login', { email, password });
      this.setToken(response.data.token); // Atualiza o token
    },
    logout() {
      this.setToken(''); // Limpa o token
      this.user = null;
      localStorage.removeItem('jwt_token');
      window.location.href = '/login';
    },
    setToken(token: string) {
      this.token = token;
      if (token) {
        localStorage.setItem('jwt_token', token); // Armazena o token no localStorage
      } else {
        localStorage.removeItem('jwt_token'); // Remove o token se estiver sendo limpado
      }
    },
  },
  getters: {
    isAuthenticated: (state) => !!state.token, // Verifica se o token existe
  },
});
