<template>
    <div class="register">

        <h1 class="text-3xl font-semibold mb-8 text-center text-red-800">Criar Conta</h1>
        <form @submit.prevent="submitForm" class="space-y-6">

            <div class="form-group">
            <label for="nome" class="block mb-2 font-medium text-gray-700">Nome Completo</label>
            <input
                id="nome"
                v-model="form.nome"
                type="text"
                required
                placeholder="Digite seu nome completo"
                class="input-field"
            />
            </div>
    
            <div class="form-group">
            <label for="email" class="block mb-2 font-medium text-gray-700">Email</label>
            <input
                id="email"
                v-model="form.email"
                type="email"
                required
                placeholder="Digite seu email"
                class="input-field"
            />
            </div>
    
            <div class="form-group">
            <label for="senha" class="block mb-2 font-medium text-gray-700">Senha</label>
            <input
                id="senha"
                v-model="form.senha"
                type="password"
                required
                placeholder="Digite sua senha"
                class="input-field"
            />
            </div>
    
            <div class="form-group">
            <label for="confirmarSenha" class="block mb-2 font-medium text-gray-700">Confirmar Senha</label>
            <input
                id="confirmarSenha"
                v-model="form.confirmarSenha"
                type="password"
                required
                placeholder="Confirme sua senha"
                class="input-field"
            />
            <span v-if="senhaError" class="error">As senhas não coincidem.</span>
            </div>
    
            <div class="footer-actions">
            <button
                type="submit"
                :disabled="senhaError"
                class="submit-button"
            >
                Criar Conta
            </button>
    
            <p class="text-center mt-4">
                Já tem uma conta? 
                <router-link to="/login" class="text-blue-600">Faça login</router-link>
            </p>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
    import { ref, watch } from 'vue';
    import { useRouter } from 'vue-router';
    import Swal from 'sweetalert2';
    import api from '../services/axios';
    
    const router = useRouter();
    
    const form = ref({
        nome: '',
        email: '',
        senha: '',
        confirmarSenha: '',
    });
    
    const senhaError = ref(false);
    
    watch(() => form.value.confirmarSenha, () => {
        senhaError.value = form.value.senha !== form.value.confirmarSenha;
    });
    
    const submitForm = async () => {
        if (senhaError.value) return;
    
        try {
        const payload = {
            name: form.value.nome,
            email: form.value.email,
            password: form.value.senha,
        };
    
        Swal.fire({
            title: 'Registrando...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
            Swal.showLoading();
            },
        });
    
        await api.post('/register', payload);
    
        await Swal.fire({
            icon: 'success',
            title: 'Conta Criada!',
            text: 'Sua conta foi criada com sucesso.',
            confirmButtonColor: '#009efb',
        });
    
        router.push('/login');
        } catch (error: any) {
        console.error('Erro ao criar conta:', error);
        await Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: 'Erro ao criar a conta. Verifique os campos e tente novamente.',
            confirmButtonColor: '#e74c3c',
        });
        }
    };
</script>

<style scoped>
    .register {
        max-width: 400px;
        margin: 0 auto;
        padding: 2rem;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    
    h1 {
        color: #333;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .input-field {
        width: 23rem;
        padding: 0.75rem;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 5px;
        outline: none;
        transition: border-color 0.2s ease-in-out;
    }
    
    .input-field:focus {
        border-color: #009efb;
    }
    
    .error {
        color: #e74c3c;
        font-size: 0.9rem;
    }
    
    .submit-button {
        width: 24rem;
        padding: 0.75rem;
        background-color: #009efb;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }
    
    .submit-button:disabled {
        background-color: #aaa;
        cursor: not-allowed;
    }
    
    .footer-actions {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .footer-actions p {
        font-size: 0.9rem;
    }
    
    .footer-actions a {
        color: #009efb;
    }
</style>
