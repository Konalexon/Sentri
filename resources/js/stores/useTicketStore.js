import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'
import echo from '@/services/echo'

export const useTicketStore = defineStore('tickets', () => {
  // State
  const tickets = ref([])
  const currentTicket = ref(null)
  const messages = ref([])
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref({
    currentPage: 1,
    lastPage: 1,
    total: 0,
    perPage: 15,
  })
  const statistics = ref({
    total: 0,
    open: 0,
    pending: 0,
    closed: 0,
  })
  const filters = ref({
    status: null,
    priority: null,
    search: '',
  })

  // Getters
  const openTickets = computed(() => 
    tickets.value.filter(t => t.status.value === 'open')
  )
  
  const pendingTickets = computed(() => 
    tickets.value.filter(t => t.status.value === 'pending')
  )
  
  const closedTickets = computed(() => 
    tickets.value.filter(t => t.status.value === 'closed')
  )

  const hasUnreadMessages = computed(() => 
    messages.value.some(m => !m.is_read)
  )

  // Actions
  async function fetchTickets(page = 1) {
    loading.value = true
    error.value = null
    
    try {
      const params = {
        page,
        per_page: pagination.value.perPage,
        ...filters.value,
      }
      
      // Remove null/empty values
      Object.keys(params).forEach(key => {
        if (params[key] === null || params[key] === '') {
          delete params[key]
        }
      })
      
      const response = await api.get('/tickets', { params })
      
      tickets.value = response.data.data
      pagination.value = {
        currentPage: response.data.meta.current_page,
        lastPage: response.data.meta.last_page,
        total: response.data.meta.total,
        perPage: response.data.meta.per_page,
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Błąd podczas pobierania zgłoszeń'
      console.error('Failed to fetch tickets:', err)
    } finally {
      loading.value = false
    }
  }

  async function fetchTicket(ticketId) {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.get(`/tickets/${ticketId}`)
      currentTicket.value = response.data.data
      messages.value = currentTicket.value.messages || []
      
      // Subscribe to ticket channel for real-time updates
      subscribeToTicket(ticketId)
      
      return currentTicket.value
    } catch (err) {
      error.value = err.response?.data?.message || 'Błąd podczas pobierania zgłoszenia'
      console.error('Failed to fetch ticket:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchMessages(ticketId) {
    try {
      const response = await api.get(`/tickets/${ticketId}/messages`)
      messages.value = response.data.data
      return messages.value
    } catch (err) {
      console.error('Failed to fetch messages:', err)
      throw err
    }
  }

  async function createTicket(ticketData) {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.post('/tickets', ticketData)
      const newTicket = response.data.data
      
      // Add to list
      tickets.value.unshift(newTicket)
      statistics.value.total++
      statistics.value.open++
      
      return newTicket
    } catch (err) {
      error.value = err.response?.data?.message || 'Błąd podczas tworzenia zgłoszenia'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function sendMessage(ticketId, content, isInternal = false, attachments = []) {
    try {
      const formData = new FormData()
      formData.append('content', content)
      formData.append('is_internal', isInternal ? '1' : '0')
      
      attachments.forEach((file, index) => {
        formData.append(`attachments[${index}]`, file)
      })
      
      const response = await api.post(`/tickets/${ticketId}/messages`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      
      const newMessage = response.data.data
      
      // Optimistically add message
      pushMessage(newMessage)
      
      return newMessage
    } catch (err) {
      console.error('Failed to send message:', err)
      throw err
    }
  }

  async function updateTicketStatus(ticketId, status) {
    try {
      const response = await api.patch(`/tickets/${ticketId}/status`, { status })
      const updatedTicket = response.data.data
      
      // Update in list
      const index = tickets.value.findIndex(t => t.id === ticketId)
      if (index !== -1) {
        tickets.value[index] = updatedTicket
      }
      
      // Update current ticket
      if (currentTicket.value?.id === ticketId) {
        currentTicket.value = updatedTicket
      }
      
      return updatedTicket
    } catch (err) {
      console.error('Failed to update ticket status:', err)
      throw err
    }
  }

  async function fetchStatistics() {
    try {
      const response = await api.get('/tickets/statistics')
      statistics.value = response.data.data
      return statistics.value
    } catch (err) {
      console.error('Failed to fetch statistics:', err)
    }
  }

  // Real-time updates
  function pushMessage(message) {
    // Check if message already exists
    const exists = messages.value.some(m => m.id === message.id)
    if (!exists) {
      messages.value.push(message)
    }
  }

  function updateTicketInList(ticketData) {
    const index = tickets.value.findIndex(t => t.id === ticketData.id)
    if (index !== -1) {
      tickets.value[index] = { ...tickets.value[index], ...ticketData }
    }
  }

  function subscribeToTicket(ticketId) {
    // Leave previous channel if exists
    echo.leave(`ticket.${currentTicket.value?.id}`)
    
    // Join new channel
    echo.private(`ticket.${ticketId}`)
      .listen('.message.sent', (e) => {
        pushMessage(e.message)
      })
      .listen('.ticket.status.changed', (e) => {
        if (currentTicket.value?.id === e.ticket_id) {
          currentTicket.value.status = e.status
          currentTicket.value.resolved_at = e.resolved_at
        }
        updateTicketInList({ id: e.ticket_id, status: e.status })
      })
      .listen('.ticket.assigned', (e) => {
        if (currentTicket.value?.id === e.ticket_id) {
          currentTicket.value.agent = e.agent
        }
      })
  }

  function subscribeToAllTickets() {
    echo.private('tickets')
      .listen('.ticket.created', (e) => {
        tickets.value.unshift(e.ticket)
        statistics.value.total++
        statistics.value.open++
      })
      .listen('.ticket.status.changed', (e) => {
        updateTicketInList({ id: e.ticket_id, status: e.status })
      })
  }

  function unsubscribeAll() {
    echo.leave('tickets')
    if (currentTicket.value) {
      echo.leave(`ticket.${currentTicket.value.id}`)
    }
  }

  function setFilters(newFilters) {
    filters.value = { ...filters.value, ...newFilters }
  }

  function clearFilters() {
    filters.value = {
      status: null,
      priority: null,
      search: '',
    }
  }

  function clearCurrentTicket() {
    if (currentTicket.value) {
      echo.leave(`ticket.${currentTicket.value.id}`)
    }
    currentTicket.value = null
    messages.value = []
  }

  return {
    // State
    tickets,
    currentTicket,
    messages,
    loading,
    error,
    pagination,
    statistics,
    filters,
    
    // Getters
    openTickets,
    pendingTickets,
    closedTickets,
    hasUnreadMessages,
    
    // Actions
    fetchTickets,
    fetchTicket,
    fetchMessages,
    createTicket,
    sendMessage,
    updateTicketStatus,
    fetchStatistics,
    pushMessage,
    updateTicketInList,
    subscribeToTicket,
    subscribeToAllTickets,
    unsubscribeAll,
    setFilters,
    clearFilters,
    clearCurrentTicket,
  }
})
