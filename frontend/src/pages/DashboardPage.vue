<template>
  <div class="dashboard">
    <div class="header">
      <h1>📋 Painel de Pedidos de Viagem</h1>

      <div class="header-actions">
        <div class="filter">
          <label for="status">Filtrar por status:</label>
          <select v-model="selectedStatus" id="status">
            <option value="">Todos</option>
            <option value="solicitado">Solicitado</option>
            <option value="aprovado">Aprovado</option>
            <option value="cancelado">Cancelado</option>
          </select>
        </div>

        <button class="create-button" @click="goToCreateOrder"> Criar Pedido </button>
        <button class="logout-button" @click="logout" :disabled="isLoggingOut">
          <span v-if="isLoggingOut" class="spinner-small"></span>
          <span v-else>Sair</span>
        </button>
      </div>
    </div>

    <div class="orders">
      <div v-if="isLoading" class="loading-spinner">
        <div class="spinner"></div>
        <p>Carregando pedidos...</p>
      </div>

      <div v-else>
        <div
          v-for="order in filteredOrders"
          :key="order.idPedido"
          class="order-card"
        >
          <div class="order-header">
            <h2>Pedido #{{ order.idPedido }}</h2>
            <span class="status-tag" :class="order.status">
              {{ statusLabel(order.status) }}
            </span>
          </div>
          <div class="order-info">
            <p><strong>Solicitante:</strong> {{ order.nomeSolicitante }}</p>
            <p><strong>Destino:</strong> {{ order.destino }}</p>
            <p><strong>Data de Ida:</strong> {{ order.dataDeIda }}</p>
            <p><strong>Data de Volta:</strong> {{ order.dataDeVolta }}</p>
          </div>
          <div class="actions">
            <UpdateStatusButton
              :orderId="order.idPedido"
              :status="order.status"
              @status-updated="fetchOrders"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref, computed, onMounted } from 'vue';
  import { useRouter } from 'vue-router';
  import api from '../services/axios';
  
  // @ts-ignore
  import UpdateStatusButton from '../components/UpdateStatusButton.vue';

  interface Order {
    idPedido: number;
    nomeSolicitante: string;
    destino: string;
    dataDeIda: string;
    dataDeVolta: string;
    status: string; // 'solicitado' | 'aprovado' | 'cancelado'
  }

  const router = useRouter();
  const orders = ref<Order[]>([]);
  const selectedStatus = ref('');
  const selectedDestino = ref('');
  const selectedDataDeIda = ref('');
  const isLoading = ref(true);
  const isLoggingOut = ref(false);

  const fetchOrders = async () => {
    isLoading.value = true;
    try {
      const response = await api.get('/orders');
      orders.value = response.data.data;
    } catch (error) {
      console.error('Erro ao buscar pedidos:', error);
    } finally {
      isLoading.value = false;
    }
  };

  const filteredOrders = computed(() => {
    return orders.value.filter((order) => {
      let match = true;

      if (selectedStatus.value && order.status !== selectedStatus.value) {
        match = false;
      }

      if (selectedDestino.value && order.destino !== selectedDestino.value) {
        match = false;
      }

      if (selectedDataDeIda.value && order.dataDeIda !== selectedDataDeIda.value) {
        match = false;
      }

      return match;
    });
  });

  const statusLabel = (status: string) => {
    switch (status) {
      case 'solicitado':
        return '🟡 Solicitado';
      case 'aprovado':
        return '🟢 Aprovado';
      case 'cancelado':
        return '🔴 Cancelado';
      default:
        return status;
    }
  };

  const goToCreateOrder = () => {
    router.push('/orders/create');
  };

  const logout = async () => {
    isLoggingOut.value = true;
    try {
      const response = await api.post('/logout');
      if (response.data.error) {
        console.error('Erro ao deslogar:', response.data.error);
        alert('Ocorreu um erro ao tentar deslogar. Por favor, tente novamente.');
      } else {
        localStorage.removeItem('jwt_token');
        router.push('/login');
      }
    } catch (error) {
      console.error('Erro na requisição de logout:', error);
      alert('Não foi possível conectar ao servidor. Verifique sua conexão.');
    } finally {
      isLoggingOut.value = false;
    }
  };

  onMounted(fetchOrders);
</script>

<style scoped>
  .dashboard {
    max-width: 1100px;
    margin: 2rem auto;
    padding: 1.5rem;
    font-family: 'Segoe UI', sans-serif;
  }

  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
  }

  .header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
  }

  h1 {
    font-size: 1.8rem;
    color: #333;
  }

  .filter {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
  }

  select {
    padding: 0.4rem 0.6rem;
    border-radius: 6px;
    border: 1px solid #ccc;
  }

  .create-button {
    background-color: #009efb;
    color: white;
    padding: 0.5rem 1.2rem;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    border: none;
    transition: background-color 0.2s ease-in-out;
  }

  .create-button:hover {
    background: #009efb;
    opacity: 0.7;
  }

  .orders {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.2rem;
  }

  .order-card {
    background: #fff;
    border-radius: 12px;
    padding: 1.2rem 1.5rem;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.07);
    transition: 0.2s ease-in-out;
  }

  .order-card:hover {
    transform: scale(1.01);
  }

  .order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
  }

  .status-tag {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: capitalize;
  }

  .status-tag.solicitado {
    background-color: #fff5cc;
    color: #a67c00;
  }

  .status-tag.aprovado {
    background-color: #d4fcd4;
    color: #2e7d32;
  }

  .status-tag.cancelado {
    background-color: #fde0e0;
    color: #c62828;
  }

  .order-info p {
    margin: 0.3rem 0;
  }

  .actions {
    margin-top: 1rem;
    display: flex;
    gap: 0.8rem;
  }

  .logout-button {
    background-color: #e53935;
    color: white;
    padding: 0.5rem 1.2rem;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    border: none;
    transition: background-color 0.2s ease-in-out;
  }

  .logout-button:hover {
    background-color: #c62828;
  }
  .loading-spinner {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 0;
  }

  .spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #ccc;
    border-top: 4px solid #1976d2;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin-bottom: 1rem;
  }

  .spinner-small {
    width: 16px;
    height: 16px;
    border: 2px solid #fff;
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
    display: inline-block;
    vertical-align: middle;
  }

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }

</style>
