<template>
    <div class="create-order">
        <h1>Criar Novo Pedido</h1>
        <h3 class="text-3xl font-semibold mb-8 text-center text-gray-800">Criar Pedido de Viagem</h3>

        <form @submit.prevent="submitForm" class="space-y-6">
            <div class="form-group">
            <label for="nomeSolicitante" class="block mb-2 font-medium text-gray-700">Nome do Solicitante</label>
            <input
                id="nomeSolicitante"
                v-model="form.nomeSolicitante"
                type="text"
                required
            />
            </div>
    
            <div class="form-group">
            <label for="destino" class="block mb-2 font-medium text-gray-700">Destino</label>
            <input
                id="destino"
                v-model="form.destino"
                type="text"
                required
            />
            </div>
    
            <div class="form-group">
            <label for="dataDeIda" class="block mb-2 font-medium text-gray-700">Data de Ida</label>
            <input
                id="dataDeIda"
                v-model="form.dataDeIda"
                type="datetime-local"
                :min="minDateTime"
                required
            />
            <span v-if="dataIdaError" class="error">A data de ida deve ser no mínimo daqui à 1 hora.</span>
            </div>
    
            <div class="form-group">
            <label for="dataDeVolta" class="block mb-2 font-medium text-gray-700">Data de Volta</label>
            <input
                id="dataDeVolta"
                v-model="form.dataDeVolta"
                type="datetime-local"
                :min="form.dataDeIda"
                required
            />
            <span v-if="dataVoltaError" class="error">A data de volta deve ser posterior à data de ida.</span>
            </div>
    
            <div class="footer-actions">
            <button
                type="submit"
                :disabled="dataIdaError || dataVoltaError"
            >
                Criar Pedido
            </button>
    
            <button
                class="back-button"
                @click="logout"
            >
                Cancelar
            </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
    import { ref, watch} from 'vue';
    import { useRouter } from 'vue-router';
    import Swal from 'sweetalert2';
    import api from '../services/axios';

    const router = useRouter();

    const form = ref({
        nomeSolicitante: '',
        destino: '',
        dataDeIda: '',
        dataDeVolta: '',
    });

    const dataVoltaError = ref(false);
    const dataIdaError = ref(false);

    const now = new Date();
    const pad = (num: number) => (num < 10 ? '0' + num : num);

    now.setMinutes(now.getMinutes() + 60);
    const minDateTime = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;

    const logout = () => {
        router.push('/dashboard');
    };

    watch(() => form.value.dataDeIda, () => {
        if (!form.value.dataDeIda) {
            dataIdaError.value = false;
            return;
        }
        const ida = new Date(form.value.dataDeIda);
        dataIdaError.value = ida < now;
    });

    watch(() => form.value.dataDeVolta, () => {
        if (!form.value.dataDeIda || !form.value.dataDeVolta) {
            dataVoltaError.value = false;
            return;
        }
        const ida = new Date(form.value.dataDeIda);
        const volta = new Date(form.value.dataDeVolta);
        dataVoltaError.value = volta <= ida;
    });

    const submitForm = async () => {
        try {
            const payload = {
            requester_name: form.value.nomeSolicitante,
            destination: form.value.destino,
            start_date: form.value.dataDeIda,
            end_date: form.value.dataDeVolta,
            };

            Swal.fire({
            title: 'Enviando pedido...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
            });
    
            await api.post('/orders', payload);
    
            await Swal.fire({
            icon: 'success',
            title: 'Pedido Criado!',
            text: 'Seu pedido de viagem foi criado com sucesso.',
            confirmButtonColor: '#009efb',
            });
    
            router.push('/dashboard');
    
        } catch (error: any) {
            console.error('Erro ao criar pedido:', error);
            await Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: 'Erro ao criar o pedido. Verifique os campos e tente novamente.',
            confirmButtonColor: '#e74c3c',
            });
        }
    };
</script>
  
<style scoped>
    .create-order {
        max-width: 600px;
        margin: 0 auto;
        padding: 2rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    input {
        width: 100%;
        padding: 0.5rem;
        font-size: 1rem;
    }
    
    button {
        padding: 0.7rem 1.1rem;
        font-weight: bold;
        background-color: #009efb;
        border: none;
        color: white;
        cursor: pointer;
        border-radius: 40px;
    }
    
    button:hover {
        background: #009efb;
        opacity: 0.7;
    }
    
    .error {
        color: #e74c3c;
        font-size: 0.9rem;
    }
    
    .back-button {
        background-color: #e53935;
        color: white;
        padding: 0.7rem 1.51rem;
        border-radius: 40px;
        font-weight: bold;
        cursor: pointer;
        border: none;
        transition: background-color 0.2s ease-in-out;
    }
    
    .back-button:hover {
        background-color: #e53935;
        opacity: 0.7;
    }
    
    .footer-actions {
        display: flex;
        gap: 23rem;
        align-items: flex-end;
        flex-wrap: wrap;
    }
</style>
