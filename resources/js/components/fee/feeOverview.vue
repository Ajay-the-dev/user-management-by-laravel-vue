<template>
    <div class="card border-0 rounded-4 shadow-sm p-2 my-5">
      <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between rounded-top-4 px-4 py-3">
        <h2 class="fw-bold mb-0 text-capitalize" style="font-size:15px;color:#111827;">{{ heading }}</h2>
        <div class="d-flex gap-2" v-if="false">
          <span class="badge rounded-pill px-3 py-2" style="font-size:11px;background:#111827;color:#fff;cursor:pointer;">All</span>
          <span class="badge rounded-pill border px-3 py-2" style="font-size:11px;color:#6b7280;cursor:pointer;">Active</span>
          <span class="badge rounded-pill border px-3 py-2" style="font-size:11px;color:#6b7280;cursor:pointer;">Inactive</span>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!allFees.length" class="text-center py-5 px-4">
        <div class="d-flex align-items-center justify-content-center rounded-3 mx-auto mb-3" style="width:56px;height:56px;background:#f3f4f6;color:#d1d5db;">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
        </div>
        <p class="fw-semibold mb-1" style="font-size:14px;color:#374151;">No fee records found</p>
        <p class="mb-0" style="font-size:12px;color:#9ca3af;">Add fee entries to see them listed here</p>
      </div>

      <!-- Table -->
      <div v-else class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr class="border-bottom">
              <th class="text-uppercase fw-bold px-4 py-3" style="font-size:11px;color:#9ca3af;letter-spacing:.5px;">#</th>
              <th class="text-uppercase fw-bold px-4 py-3" style="font-size:11px;color:#9ca3af;letter-spacing:.5px;">Batch</th>
              <th class="text-uppercase fw-bold px-4 py-3" style="font-size:11px;color:#9ca3af;letter-spacing:.5px;">Type</th>
              <th class="text-uppercase fw-bold px-4 py-3" style="font-size:11px;color:#9ca3af;letter-spacing:.5px;">Amount</th>
              <th class="text-uppercase fw-bold px-4 py-3" style="font-size:11px;color:#9ca3af;letter-spacing:.5px;">Due date</th>
              <th class="text-uppercase fw-bold px-4 py-3" style="font-size:11px;color:#9ca3af;letter-spacing:.5px;">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(fee, index) in allFees" :key="fee.id || index">
              <td class="px-4 py-3 fw-bold" style="font-size:12px;color:#9ca3af;">{{ index + 1 }}</td>
              <td class="px-4 py-3 fw-semibold" style="font-size:13px;color:#111827;">{{ fee.batches?.name || 'N/A' }}</td>
              <td class="px-4 py-3">
                <span class="badge rounded-pill px-3 py-1" style="background:#eff6ff;color:#3b82f6;font-size:11px;">{{ fee.type }}</span>
              </td>
              <td class="px-4 py-3 fw-bold" style="font-size:14px;color:#059669;">$ {{ fee.amount }}</td>
              <td class="px-4 py-3" style="font-size:13px;color:#374151;">{{ fee.due ?? '-' }}</td>
              <td class="px-4 py-3">
                <span class="d-inline-flex align-items-center gap-2 fw-semibold text-capitalize" style="font-size:12px;" :style="fee.status === 'ACTIVE' ? 'color:#059669;' : 'color:#d97706;'">
                  <span class="rounded-circle d-inline-block" style="width:7px;height:7px;flex-shrink:0;" :style="fee.status === 'ACTIVE' ? 'background:#10b981;box-shadow:0 0 0 2px #d1fae5;' : 'background:#f59e0b;box-shadow:0 0 0 2px #fef3c7;'"></span>
                  {{ fee.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
</template>
<script setup>
const props = defineProps({
    allFees:{
        type : Array,
        default:[]
    },
    heading:{
        type:String,
        default:''
    },
    interactive:{
        type:Boolean,
        default:false
    }
})
</script>
<style scoped>
</style>