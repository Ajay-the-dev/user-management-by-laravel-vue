<template>
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center">
        <div class="icon-box bg-primary-subtle text-primary rounded-3 p-2 me-3">
          <i class="fa fa-bullhorn"></i>
        </div>
        <h5 class="fw-bold m-0 text-dark">Important Notices</h5>
      </div>
      <button class="btn btn-sm btn-light text-primary fw-semibold">View All</button>
    </div>

    <div class="card-body px-4 pb-4">
      <div class="notice-list">
        
        <div v-for="(notice, index) in notices" :key="index" 
             class="notice-item p-3 mb-3 border-start border-4 rounded-3"
             :class="notice.type === 'urgent' ? 'border-danger bg-danger-subtle' : 'border-primary bg-light'">
          <div class="d-flex justify-content-between align-items-start mb-1">
            <h6 class="fw-bold mb-0 text-dark">{{ notice.title }}</h6>
            <span class="badge rounded-pill text-uppercase" 
                  :class="notice.type === 'urgent' ? 'bg-danger' : 'bg-primary'">
              {{ notice.tag }}
            </span>
          </div>
          <p class="small text-secondary mb-2">{{ notice.description }}</p>
          <div class="d-flex align-items-center text-muted small">
            <i class="fa fa-calendar-alt me-2"></i>
            <span>{{ notice.date }}</span>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// Fake data for the notice board
const notices = ref([
  {
    title: 'Mid-Term Examination Schedule',
    description: 'The mid-term exams for the Spring 2026 batch will commence from May 15th. Please check your roll numbers.',
    date: 'April 20, 2026',
    tag: 'Academic',
    type: 'normal'
  },
  {
    title: 'Urgent: Insurance Renewal',
    description: 'Students with expiring insurance policies must visit the office by Friday to avoid visa complications.',
    date: 'April 24, 2026',
    tag: 'Urgent',
    type: 'urgent'
  },
  {
    title: 'Campus Maintenance Alert',
    description: 'The main library will be closed this weekend for system upgrades and general maintenance.',
    date: 'April 22, 2026',
    tag: 'Facility',
    type: 'normal'
  }
])
</script>

<style scoped>
.icon-box {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.notice-item {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  cursor: pointer;
}

.notice-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.bg-primary-subtle {
  background-color: rgba(99, 102, 241, 0.1) !important;
}

.bg-danger-subtle {
  background-color: rgba(239, 68, 68, 0.05) !important;
}

.text-primary {
  color: #6366f1 !important;
}

.btn-light {
  background-color: #f1f5f9;
  border: none;
}
</style>