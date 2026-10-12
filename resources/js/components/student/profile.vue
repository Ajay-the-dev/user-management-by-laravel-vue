<template>
  <div class="sp-wrap">

    <!-- Header -->
    <div class="sp-header">
      <img class="sp-avatar" :src="student.profile_picture" :alt="student.name + ' photo'"  v-if="student.profile_picture !== null"/>
      <div class="sp-avatar student-avatar d-flex align-items-center justify-content-center fw-bold flex-shrink-0 text-white" v-else>{{ initials }}</div>
      <div>
        <p class="sp-name">{{ student.name }}</p>
        <p class="sp-meta">@{{ student.username }} · Roll No: {{ student.rollNo }}</p>
        <div class="sp-badges">
          <span class="badge badge-blue">{{ student.role }}</span>
          <span class="badge badge-blue">{{ student.course }} — {{ student.university }}</span>
          <span class="badge badge-green">{{ student.batches?.name }}</span>
          <span class="badge badge-amber">Semester {{ student.batches?.semester }}</span>
        </div>
      </div>
    </div>

    <div class="sp-grid">

      <!-- Personal Info -->
      <div class="sp-card">
        <p class="sp-card-title">Personal info</p>
        <div class="sp-row"><span class="sp-label">Email</span><span class="sp-value">{{ student.email }}</span></div>
        <div class="sp-row"><span class="sp-label">Mobile</span><span class="sp-value">{{ student.mobile }}</span></div>
        <div class="sp-row"><span class="sp-label">Date of birth</span><span class="sp-value">{{ formatDate(student.dob) }}</span></div>
        <div class="sp-row"><span class="sp-label">Gender</span><span class="sp-value">{{ capitalize(student.gender) }}</span></div>
        <div class="sp-row"><span class="sp-label">Address</span><span class="sp-value">{{ formatAddress(student.address?.[0]) }}</span></div>
        <div class="sp-row"><span class="sp-label">Department</span><span class="sp-value">{{ student.department }}</span></div>
      </div>

      <!-- Visa & Insurance -->
      <div class="sp-card">
        <p class="sp-card-title">Visa & insurance</p>
        <div class="sp-row"><span class="sp-label">Visa type</span><span class="sp-value">{{ capitalize(student.visaType) }}</span></div>
        <div class="sp-row"><span class="sp-label">Visa status</span><span class="sp-value"><span :class="statusBadge(student.visaStatus)">{{ capitalize(student.visaStatus) }}</span></span></div>
        <div class="sp-row"><span class="sp-label">Visa expiry</span><span class="sp-value" :class="{ warn: isExpiringSoon(student.visaExpiryDate) }">{{ formatDate(student.visaExpiryDate) || '—' }}</span></div>
        <div class="sp-row"><span class="sp-label">Insurance</span><span class="sp-value"><span :class="statusBadge(student.insuranceStatus)">{{ capitalize(student.insuranceStatus) }}</span></span></div>
        <div class="sp-row"><span class="sp-label">Insurance expiry</span><span class="sp-value" :class="{ warn: isExpiringSoon(student.insuranceExpiryDate) }">{{ formatDate(student.insuranceExpiryDate) || '—' }}</span></div>
        <div class="sp-row"><span class="sp-label">Location</span><span class="sp-value">{{ student.location }}</span></div>
      </div>

      <!-- Passport -->
      <div class="sp-card">
        <p class="sp-card-title">Passport</p>
        <div class="sp-row"><span class="sp-label">Number</span><span class="sp-value">{{ student.passportNumber ?? "—" }}</span></div>
        <div class="sp-row"><span class="sp-label">Issuing country</span><span class="sp-value">{{ student.passportIssuingCountry ?? "—" }}</span></div>
        <div class="sp-row"><span class="sp-label">Issue date</span><span class="sp-value">{{ formatDate(student.passportIssueDate) ?? "—" }}</span></div>
        <div class="sp-row"><span class="sp-label">Expiry date</span><span class="sp-value" :class="{ warn: isExpiringSoon(student.passportExpiryDate) }">{{ formatDate(student.passportExpiryDate) ?? "—" }}</span></div>
      </div>

      <!-- Parent Details -->
      <div class="sp-card">
        <p class="sp-card-title">Parent / guardian</p>
        <div v-for="(parent, i) in student.parentDetails" :key="i" class="sp-parent">
          <div class="sp-row"><span class="sp-label">Name</span><span class="sp-value">{{ capitalize(parent.name) }}</span></div>
          <div class="sp-row"><span class="sp-label">Relation</span><span class="sp-value">{{ parent.relation }}</span></div>
          <div class="sp-row"><span class="sp-label">Email</span><span class="sp-value">{{ parent.email }}</span></div>
          <div class="sp-row"><span class="sp-label">Mobile</span><span class="sp-value">{{ parent.mobile }}</span></div>
        </div>
        <div class="sp-parent">
            <span class="sp-label">No data present !</span>
        </div>
      </div>

      <!-- Batch Info -->
      <div class="sp-card">
        <p class="sp-card-title">Batch info</p>
        <div class="sp-row"><span class="sp-label">Batch name</span><span class="sp-value">{{ student.batches?.name }}</span></div>
        <div class="sp-row"><span class="sp-label">Semester</span><span class="sp-value">{{ student.batches?.semester }}</span></div>
        <div class="sp-row"><span class="sp-label">Status</span><span class="sp-value"><span :class="statusBadge(student.batches?.status)">{{ capitalize(student.batches?.status) }}</span></span></div>
        <div class="sp-row"><span class="sp-label">Start date</span><span class="sp-value">{{ formatDate(student.batches?.start_date) }}</span></div>
        <div class="sp-row"><span class="sp-label">End date</span><span class="sp-value">{{ formatDate(student.batches?.end_date) }}</span></div>
        <div class="sp-row"><span class="sp-label">Passed out</span><span class="sp-value">{{ student.batches?.is_passout ? 'Yes' : 'No' }}</span></div>
      </div>

    </div>
  </div>
</template>

<script setup>

import { ref,onMounted,computed } from "vue";
import { useUserStore } from '@/stores/userStore'
const userStore = useUserStore()

const initials = computed(() =>
  userStore.allData.name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase()
)
const student = computed(()=>{
    return userStore.allData
})

const formatDate = (val) => {
  if (!val) return null
  return new Date(val).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
}

const capitalize = (val) => val ? val.charAt(0).toUpperCase() + val.slice(1).toLowerCase() : '—'

const formatAddress = (addr) => addr ? `${addr.street}, ${addr.city}, ${addr.country}` : '—'

const isExpiringSoon = (date) => {
  if (!date) return false
  const diff = new Date(date) - new Date()
  return diff > 0 && diff < 1000 * 60 * 60 * 24 * 90 // within 90 days
}

const statusBadge = (status) => ({
  badge: true,
  'badge-green': status === 'active',
  'badge-red': status === 'inactive' || status === 'expired',
  'badge-amber': status === 'pending',
})
</script>

<style scoped lang="scss">
@use "abstracts" as *;

.sp-wrap { padding: 0.25rem 0 1rem; }

.sp-header {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  margin-bottom: 1.5rem;
  padding: 1.5rem;
  border-radius: $radius-lg;
  background: $gradient-hero;
  box-shadow: $shadow-md;
  color: #fff;

  @include below(sm) {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1.25rem;
  }
}

.sp-avatar {
  @include square(4.5rem);
  flex-shrink: 0;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid rgba(255, 255, 255, 0.25);
}

.student-avatar {
  background: $gradient-brand;
  font-size: 0.9375rem;
  letter-spacing: 1px;
}

.sp-name {
  margin: 0 0 0.25rem;
  font-size: 1.25rem;
  font-weight: 700;
}

.sp-meta {
  margin: 0;
  font-size: $fs-sm;
  color: $sky-200;
}

.sp-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 0.375rem;
  margin-top: 0.5rem;
}

.badge {
  padding: 0.1875rem 0.625rem;
  border-radius: 999px;
  font-size: $fs-xs;
  font-weight: 600;
}

.badge-blue  { background: $sky-100;     color: $sky-800; }
.badge-green { background: $emerald-100; color: $emerald-700; }
.badge-amber { background: $amber-100;   color: #854f0b; }
.badge-red   { background: $red-100;     color: #a32d2d; }

.sp-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(min(20rem, 100%), 1fr));
  gap: 1rem;
}

.sp-card {
  padding: 1.125rem 1.25rem;
  background: $surface;
  border: 1px solid $border-soft;
  border-radius: $radius;
  box-shadow: $shadow-xs;
  @include hover-lift($shadow-sm);
}

.sp-card-title {
  @include eyebrow($color: $sky-700);
  margin: 0 0 0.75rem;
}

.sp-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 0.75rem;
  padding: 0.375rem 0;
  border-bottom: 1px solid $slate-100;

  &:last-child { border-bottom: none; }
}

.sp-label {
  font-size: $fs-sm;
  color: $text-muted;
  white-space: nowrap;
}

.sp-value {
  font-size: $fs-sm;
  font-weight: 500;
  text-align: right;
  word-break: break-word;

  &.warn { color: $red-600; }
}

.sp-parent {
  padding: 0.625rem 0.75rem;
  margin-top: 0.5rem;
  border-radius: $radius-sm;
  background: $surface-muted;

  &:first-child { margin-top: 0; }
}
</style>