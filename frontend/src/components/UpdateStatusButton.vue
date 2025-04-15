<template>
  <div>
    <button
      v-if="status !== 'aprovado'"
      class="approve"
      @click="confirmUpdate('aprovado')"
    >
      Aprovar
    </button>
    <button
      v-if="status !== 'cancelado'"
      class="cancel"
      @click="confirmUpdate('cancelado')"
    >
      Cancelar
    </button>
  </div>
</template>

<script setup lang="ts">
  import { defineProps, defineEmits } from 'vue';
  import api from '../services/axios';
  import Swal from 'sweetalert2';

  const props = defineProps({
    orderId: {
      type: Number,
      required: true
    },
    status: {
      type: String,
      required: true
    }
  });

  const emit = defineEmits<{
    (event: 'status-updated', updatedOrder: any): void;
  }>();

  const confirmUpdate = async (newStatus: string) => {
    const { isConfirmed } = await Swal.fire({
      title: `Tem certeza que deseja ${newStatus === 'aprovado' ? 'aprovar' : 'cancelar'} este pedido?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sim',
      cancelButtonText: 'Não',
      confirmButtonColor: newStatus === 'aprovado' ? '#27ae60' : '#e74c3c'
    });

    if (!isConfirmed) return;

    Swal.fire({
      title: 'Atualizando status...',
      allowOutsideClick: false,
      allowEscapeKey: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    try {
      const response = await api.patch(`/orders/${props.orderId}/status`, {
        status: newStatus
      });

      emit('status-updated', response.data);

      await Swal.fire({
        icon: 'success',
        title: 'Status atualizado!',
        text: `O pedido foi ${newStatus === 'aprovado' ? 'aprovado' : 'cancelado'} com sucesso.`,
        confirmButtonColor: '#3085d6'
      });

    } catch (error: any) {
      if (error.response?.status === 403) {
        await Swal.fire({
          icon: 'error',
          title: 'Ação não permitida',
          text: error.response.data.message,
          confirmButtonColor: '#e74c3c'
        });
      } else if (error.response?.status === 400 && error.response.data.message === 'Pedidos só podem ser cancelados com no mínimo 2 dias de antecedência.') {
        await Swal.fire({
          icon: 'error',
          title: 'Erro ao cancelar pedido',
          text: error.response.data.message,
          confirmButtonColor: '#e74c3c',
        });
      } else {
        await Swal.fire({
          icon: 'error',
          title: 'Erro',
          text: 'Erro ao atualizar o status do pedido. Tente novamente mais tarde.',
          confirmButtonColor: '#e74c3c'
        });
      }

      console.error('Erro ao atualizar status:', error);
    }
  };
</script>


<style scoped>
  button {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
  }

  .approve {
    background-color: #27ae60;
    color: white;
  }

  .approve:hover {
    background-color: #219653;
  }

  .cancel {
    background-color: #e74c3c;
    color: white;
  }

  .cancel:hover {
    background-color: #c0392b;
  }
</style>
