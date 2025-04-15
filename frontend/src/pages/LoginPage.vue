<template>
  <div class="login-container">
    <div class="login-card">
      <div style="text-align: center" class="login-text">
        <h2>Onfly</h2>
      </div>

      <form @submit.prevent="login">
        <input v-model="email" placeholder="Email" type="email" required />
        <input v-model="password" placeholder="Senha" type="password" required />

        <button type="submit" :disabled="loading">
          <span v-if="loading" class="spinner"></span>
          <span v-else>Vamos</span>
        </button>
      </form>

      <p v-if="error" class="error-message">{{ error }}</p>

      <p class="register-link">
        Não tem uma conta?
        <router-link to="/cadastrar">Cadastre-se</router-link>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/axios';
import { useAuthStore } from '../stores/auth';

const email = ref('');
const password = ref('');
const loading = ref(false);
const error = ref('');
const router = useRouter();
const auth = useAuthStore();

const login = async (event?: Event) => {
  if (event) event.preventDefault();

  loading.value = true;
  error.value = '';

  try {
    const response = await api.post('login', {
      email: email.value,
      password: password.value,
    });

    if (response.data.access_token) {
      auth.setToken(response.data.access_token);
      router.push('/dashboard');
    } else {
      throw new Error('Token não encontrado na resposta');
    }
  } catch (err) {
    error.value = 'Usuário ou senha inválidos';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
  .login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #f5f5f5;
  }

  .login-card {
    background-color: white;
    padding: 2.5rem;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
  }

  .login-text {
    width: 24.5rem;
    font-family: "Poppins", "Rubik", sans-serif;
    overflow-x: hidden;
    color: #737373;
    font-weight: 200;
  }

  h2 {
    text-align: center;
    margin-bottom: 1.5rem;
  }

  input {
    width: 22.5rem;
    padding: 0.75rem;
    margin-bottom: 1rem;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 1rem;
  }

  button {
    width: 24rem;
    padding: 0.75rem;
    background-color: #009efb;
    color: white;
    font-weight: bold;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.2s ease-in-out;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  button:hover {
    background: #009efb;
    opacity: 0.7;
  }

  button:disabled {
    background-color: #b0bec5;
    cursor: not-allowed;
  }

  .spinner {
    border: 3px solid #f3f3f3;
    border-top: 3px solid white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  .register-link {
    margin-top: 1rem;
    text-align: center;
  }

  .register-link a {
    color: #3085d6;
    text-decoration: none;
    font-weight: bold;
  }

  .register-link a:hover {
    text-decoration: underline;
  }

  .error-message {
    color: #e74c3c;
    text-align: center;
    margin-top: 1rem;
  }
</style>
