<template>
  <LoadingScreen />
  <div class="admin-shell">
    <!-- ── SIDEBAR ─────────────────────────── -->
    <aside
      class="sidebar"
      :class="{ collapsed: sidebarCollapsed, open: sidebarOpen }"
    >
      <!-- Logo -->
      <div class="sidebar-logo">
        <img
          src="/assets/images/logo.png"
          alt="Palengke Online"
          class="sidebar-logo-img"
        />
        <div class="sidebar-logo-text">
          <span class="sidebar-brand">Palengke Online</span>
          <span class="sidebar-brand-sub">Admin Portal</span>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="sidebar-nav">
        <div class="nav-section-label">Overview</div>
        <NuxtLink to="/dashboard" class="nav-item" active-class="active">
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <rect x="3" y="3" width="7" height="7" />
              <rect x="14" y="3" width="7" height="7" />
              <rect x="14" y="14" width="7" height="7" />
              <rect x="3" y="14" width="7" height="7" />
            </svg>
          </span>
          <span class="nav-label">Dashboard</span>
        </NuxtLink>

        <NuxtLink to="/map" class="nav-item" active-class="active">
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21" />
              <line x1="9" y1="3" x2="9" y2="18" />
              <line x1="15" y1="6" x2="15" y2="21" />
            </svg>
          </span>
          <span class="nav-label">Live Map</span>
          <span class="nav-badge live">Live</span>
        </NuxtLink>

        <div class="nav-section-label">Management</div>

        <NuxtLink
          to="/approvals"
          class="nav-item"
          active-class="active"
          v-if="auth.user?.role === 'superadmin'"
        >
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M9 12l2 2 4-4" />
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
            </svg>
          </span>
          <span class="nav-label">Approvals</span>
          <span class="nav-badge count" v-if="pendingApprovals > 0">{{
            pendingApprovals
          }}</span>
        </NuxtLink>

        <NuxtLink to="/orders" class="nav-item" active-class="active">
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
              <line x1="3" y1="6" x2="21" y2="6" />
              <path d="M16 10a4 4 0 0 1-8 0" />
            </svg>
          </span>
          <span class="nav-label">Orders</span>
          <span class="nav-badge count" v-if="pendingOrders > 0">{{
            pendingOrders
          }}</span>
        </NuxtLink>

        <NuxtLink to="/sellers" class="nav-item" active-class="active">
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
              <polyline points="9 22 9 12 15 12 15 22" />
            </svg>
          </span>
          <span class="nav-label">Sellers</span>
        </NuxtLink>

        <NuxtLink to="/drivers" class="nav-item" active-class="active">
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <circle cx="12" cy="12" r="10" />
              <path d="M12 8v4l3 3" />
            </svg>
          </span>
          <span class="nav-label">Drivers</span>
        </NuxtLink>

        <NuxtLink to="/customers" class="nav-item" active-class="active">
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
              <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
          </span>
          <span class="nav-label">Customers</span>
        </NuxtLink>

        <NuxtLink to="/products" class="nav-item" active-class="active">
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <line x1="8" y1="6" x2="21" y2="6" />
              <line x1="8" y1="12" x2="21" y2="12" />
              <line x1="8" y1="18" x2="21" y2="18" />
              <line x1="3" y1="6" x2="3.01" y2="6" />
              <line x1="3" y1="12" x2="3.01" y2="12" />
              <line x1="3" y1="18" x2="3.01" y2="18" />
            </svg>
          </span>
          <span class="nav-label">Products</span>
        </NuxtLink>

        <NuxtLink to="/categories" class="nav-item" active-class="active">
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path
                d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"
              />
              <line x1="7" y1="7" x2="7.01" y2="7" />
            </svg>
          </span>
          <span class="nav-label">Categories</span>
        </NuxtLink>

        <NuxtLink to="/banners" class="nav-item" active-class="active">
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <rect x="3" y="3" width="18" height="18" rx="2" />
              <circle cx="8.5" cy="8.5" r="1.5" />
              <path d="M21 15l-5-5L5 21" />
            </svg>
          </span>
          <span class="nav-label">Banners</span>
        </NuxtLink>

        <NuxtLink to="/promos" class="nav-item" active-class="active">
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path
                d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"
              />
              <line x1="7" y1="7" x2="7.01" y2="7" />
            </svg>
          </span>
          <span class="nav-label">Promos</span>
        </NuxtLink>

        <NuxtLink to="/banner-requests" class="nav-item" active-class="active">
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path
                d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"
              />
              <line x1="8" y1="10" x2="16" y2="10" />
              <line x1="12" y1="7" x2="12" y2="13" />
            </svg>
          </span>
          <span class="nav-label">Banner Requests</span>
        </NuxtLink>

        <div class="nav-section-label">Finance</div>

        <NuxtLink to="/analytics" class="nav-item" active-class="active">
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <line x1="18" y1="20" x2="18" y2="10" />
              <line x1="12" y1="20" x2="12" y2="4" />
              <line x1="6" y1="20" x2="6" y2="14" />
            </svg>
          </span>
          <span class="nav-label">Analytics</span>
        </NuxtLink>

        <NuxtLink to="/payouts" class="nav-item" active-class="active">
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <rect x="2" y="5" width="20" height="14" rx="2" />
              <line x1="2" y1="10" x2="22" y2="10" />
            </svg>
          </span>
          <span class="nav-label">Payouts</span>
        </NuxtLink>

        <NuxtLink to="/disputes" class="nav-item" active-class="active">
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="8" x2="12" y2="12" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
          </span>
          <span class="nav-label">Disputes</span>
          <span class="nav-badge warn" v-if="openDisputes > 0">{{
            openDisputes
          }}</span>
        </NuxtLink>

        <NuxtLink to="/complaints" class="nav-item" active-class="active">
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
              <line x1="12" y1="9" x2="12" y2="13" />
              <line x1="12" y1="17" x2="12.01" y2="17" />
            </svg>
          </span>
          <span class="nav-label">Complaints</span>
          <span class="nav-badge warn" v-if="pendingComplaints > 0">{{
            pendingComplaints
          }}</span>
        </NuxtLink>

        <NuxtLink to="/reports" class="nav-item" active-class="active">
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path
                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
              />
              <polyline points="14 2 14 8 20 8" />
              <line x1="16" y1="13" x2="8" y2="13" />
              <line x1="16" y1="17" x2="8" y2="17" />
              <polyline points="10 9 9 9 8 9" />
            </svg>
          </span>
          <span class="nav-label">Reports</span>
          <span class="nav-badge count" v-if="openReports > 0">{{
            openReports
          }}</span>
        </NuxtLink>

        <div class="nav-section-label">System</div>

        <NuxtLink to="/notifications" class="nav-item" active-class="active">
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
              <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
          </span>
          <span class="nav-label">Notifications</span>
        </NuxtLink>

        <NuxtLink to="/notices" class="nav-item" active-class="active">
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path
                d="M11 22q-4 0-7-3t-3-7q0-1 .3-2.2Q2 8.2 3 7l9-5 9 5q1 .9 1.7 2.8.3 1.2.3 2.2 0 4-3 7t-7 3"
              />
              <line x1="12" y1="11" x2="12" y2="17" />
              <line x1="9" y1="14" x2="15" y2="14" />
            </svg>
          </span>
          <span class="nav-label">Notices</span>
        </NuxtLink>

        <NuxtLink
          to="/admin-users"
          class="nav-item"
          active-class="active"
          v-if="auth.user?.role === 'superadmin'"
        >
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
              <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
          </span>
          <span class="nav-label">Admin Users</span>
        </NuxtLink>

        <NuxtLink
          v-show="auth.user?.role === 'superadmin'"
          to="/settings"
          class="nav-item"
          active-class="active"
        >
          <span class="nav-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <circle cx="12" cy="12" r="3" />
              <path
                d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"
              />
            </svg>
          </span>
          <span class="nav-label">Settings</span>
        </NuxtLink>
        <NuxtLink
          v-show="auth.user?.role === 'superadmin'"
          to="/settings/fees"
          class="nav-item sub"
          active-class="active"
        >
          <span class="nav-label" style="padding-left: 30px; font-size: 12px"
            >Delivery Fees</span
          >
        </NuxtLink>
        <NuxtLink
          v-show="auth.user?.role === 'superadmin'"
          to="/settings/commissions"
          class="nav-item sub"
          active-class="active"
        >
          <span class="nav-label" style="padding-left: 30px; font-size: 12px"
            >Commissions</span
          >
        </NuxtLink>
        <NuxtLink
          v-show="auth.user?.role === 'superadmin'"
          to="/settings/payments"
          class="nav-item sub"
          active-class="active"
        >
          <span class="nav-label" style="padding-left: 30px; font-size: 12px"
            >Payments</span
          >
        </NuxtLink>
        <NuxtLink
          v-show="auth.user?.role === 'superadmin'"
          to="/settings/locations"
          class="nav-item sub"
          active-class="active"
        >
          <span class="nav-label" style="padding-left: 30px; font-size: 12px"
            >Locations</span
          >
        </NuxtLink>
      </nav>

      <!-- Sidebar Footer: Admin Profile -->
      <div class="sidebar-user">
        <div class="user-avatar">
          <span>{{ auth.user?.name?.charAt(0) || "A" }}</span>
        </div>
        <div class="user-info">
          <span class="user-name">{{ auth.user?.name || "Admin" }}</span>
          <span class="user-role"
            >{{ auth.user?.email || "admin@palengke.com" }} ·
            {{ auth.user?.role || "none" }}</span
          >
        </div>
        <button class="user-logout" @click="handleLogout" title="Logout">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" y1="12" x2="9" y2="12" />
          </svg>
        </button>
      </div>
    </aside>

    <div
      class="sidebar-overlay"
      :class="{ open: sidebarOpen }"
      @click="sidebarOpen = false"
    ></div>

    <button
      class="sidebar-collapse-btn"
      @click="sidebarCollapsed = !sidebarCollapsed"
      :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
    >
      <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        :class="{ flipped: sidebarCollapsed }"
      >
        <polyline points="15 18 9 12 15 6" />
      </svg>
    </button>

    <!-- ── MAIN CONTENT AREA ───────────────── -->
    <div class="main-area">
      <!-- Topbar -->
      <header class="topbar">
        <div class="topbar-left">
          <button
            class="hamburger-btn"
            @click="sidebarOpen = !sidebarOpen"
            title="Toggle menu"
          >
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <line x1="3" y1="6" x2="21" y2="6" />
              <line x1="3" y1="12" x2="21" y2="12" />
              <line x1="3" y1="18" x2="21" y2="18" />
            </svg>
          </button>
          <h1 class="topbar-title">{{ pageTitle }}</h1>
          <div class="topbar-breadcrumb">
            <span>Admin</span>
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <polyline points="9 18 15 12 9 6" />
            </svg>
            <span class="breadcrumb-current">{{ pageTitle }}</span>
          </div>
        </div>

        <div class="topbar-right">
          <!-- Search -->
          <div class="topbar-search" ref="searchRef">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <circle cx="11" cy="11" r="8" />
              <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search orders, sellers…"
              class="search-input"
              @input="onSearch"
              @keydown="onSearchKeydown"
              @focus="searchFocused = true"
            />
            <kbd class="search-kbd">⌘K</kbd>
            <div class="search-dropdown" v-if="searchFocused && searchQuery">
              <div v-if="searchResults.length === 0" class="search-empty">
                No results found
              </div>
              <template v-for="(group, gi) in searchResults" :key="gi">
                <div class="search-group-label">{{ group.label }}</div>
                <div
                  v-for="(item, ii) in group.items"
                  :key="gi + '-' + ii"
                  class="search-item"
                  :class="{ highlighted: searchHighlight === gi + '-' + ii }"
                  @mousedown.prevent="goToResult(item)"
                  @mouseenter="searchHighlight = gi + '-' + ii"
                >
                  <span class="search-item-icon">{{ group.icon }}</span>
                  <div class="search-item-body">
                    <span class="search-item-title">{{ item.title }}</span>
                    <span class="search-item-sub">{{ item.sub }}</span>
                  </div>
                </div>
              </template>
            </div>
          </div>

          <!-- Notifications Bell -->
          <button class="topbar-btn" @click="toggleNotifPanel">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
              <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
            <span class="btn-dot" v-if="unreadNotifs > 0">{{
              unreadNotifs
            }}</span>
          </button>

          <!-- Admin Avatar -->
          <button class="topbar-avatar" @click="toggleUserMenu">
            <span>{{ auth.user?.name?.charAt(0) || "A" }}</span>
          </button>

          <!-- User dropdown -->
          <div
            class="user-dropdown"
            v-if="showUserMenu"
            v-click-outside="() => (showUserMenu = false)"
          >
            <div class="dropdown-header">
              <strong>{{ auth.user?.name || "Admin" }}</strong>
              <span>{{ auth.user?.email || "admin@palengke.com" }}</span>
            </div>
            <div class="dropdown-divider"></div>
            <NuxtLink
              to="/settings"
              class="dropdown-item"
              @click="showUserMenu = false"
            >
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <circle cx="12" cy="12" r="3" />
                <path
                  d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"
                />
              </svg>
              Settings
            </NuxtLink>
            <div class="dropdown-divider"></div>
            <button
              class="dropdown-item"
              @click="
                theme.toggle();
                showUserMenu = false;
              "
            >
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
              </svg>
              {{ theme.isDark ? "Light Mode" : "Dark Mode" }}
            </button>
            <div class="dropdown-divider"></div>
            <button class="dropdown-item danger" @click="handleLogout">
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                <polyline points="16 17 21 12 16 7" />
                <line x1="21" y1="12" x2="9" y2="12" />
              </svg>
              Logout
            </button>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="page-content">
        <slot />
      </main>
    </div>

    <!-- Notification Panel (slide-in) -->
    <div
      class="notif-overlay"
      v-if="showNotifPanel"
      @click="showNotifPanel = false"
    ></div>
    <div class="notif-panel" :class="{ open: showNotifPanel }">
      <div class="notif-panel-header">
        <h3>Notifications</h3>
        <button @click="showNotifPanel = false">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
          </svg>
        </button>
      </div>
      <div class="notif-list">
        <div class="notif-item unread" v-for="n in notifications" :key="n.id">
          <div class="notif-dot" :class="n.type"></div>
          <div class="notif-body">
            <p class="notif-text">{{ n.message }}</p>
            <span class="notif-time">{{ n.time }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const route = useRoute();
const router = useRouter();
const auth = useAuthStore();

const theme = useThemeStore();
if (import.meta.client) theme.init();

const sidebarCollapsed = ref(false);
const sidebarOpen = ref(false);
const showUserMenu = ref(false);
const showNotifPanel = ref(false);

// Global search
const searchQuery = ref("");
const searchFocused = ref(false);
const searchHighlight = ref("");
const searchRef = ref(null);
let searchTimer = null;

const searchResults = computed(() => {
  const q = searchQuery.value.toLowerCase().trim();
  if (!q) return [];
  const results = [];

  // Orders
  const ordersStore = useOrdersStore();
  const matchedOrders = ordersStore.orders.filter(
    (o) =>
      o.id.toLowerCase().includes(q) || o.customer.toLowerCase().includes(q),
  );
  if (matchedOrders.length) {
    results.push({
      label: "Orders",
      icon: "📦",
      items: matchedOrders.map((o) => ({
        title: "#" + o.id,
        sub: o.customer + " — ₱" + o.total.toLocaleString(),
        to: "/orders/" + o.id,
      })),
    });
  }

  // Sellers
  const sellersStore = useSellersStore();
  const matchedSellers = sellersStore.sellers.filter(
    (s) =>
      s.name.toLowerCase().includes(q) || s.owner.toLowerCase().includes(q),
  );
  if (matchedSellers.length) {
    results.push({
      label: "Sellers",
      icon: "🏪",
      items: matchedSellers.map((s) => ({
        title: s.name,
        sub: s.owner + " · " + s.category,
        to: "/sellers/" + s.id,
      })),
    });
  }

  // Customers
  const customersStore = useCustomersStore();
  const matchedCustomers = customersStore.customers.filter(
    (c) =>
      c.name.toLowerCase().includes(q) || c.email.toLowerCase().includes(q),
  );
  if (matchedCustomers.length) {
    results.push({
      label: "Customers",
      icon: "👤",
      items: matchedCustomers.map((c) => ({
        title: c.name,
        sub: c.email,
        to: "/customers/" + c.id,
      })),
    });
  }

  // Drivers
  const driversStore = useDriversStore();
  const matchedDrivers = driversStore.drivers.filter((d) =>
    d.name.toLowerCase().includes(q),
  );
  if (matchedDrivers.length) {
    results.push({
      label: "Drivers",
      icon: "🏍️",
      items: matchedDrivers.map((d) => ({
        title: d.name,
        sub: d.vehicle + " · " + d.plate,
        to: "/drivers/" + d.id,
      })),
    });
  }

  return results;
});

function onSearch() {
  searchHighlight.value = "";
}

function onSearchKeydown(e) {
  const flat = [];
  searchResults.value.forEach((g) =>
    g.items.forEach((item, ii) => flat.push(g.label + "-" + ii)),
  );

  if (e.key === "ArrowDown") {
    e.preventDefault();
    const idx = flat.indexOf(searchHighlight.value);
    const next = idx < flat.length - 1 ? idx + 1 : 0;
    searchHighlight.value = flat[next];
  } else if (e.key === "ArrowUp") {
    e.preventDefault();
    const idx = flat.indexOf(searchHighlight.value);
    const prev = idx > 0 ? idx - 1 : flat.length - 1;
    searchHighlight.value = flat[prev];
  } else if (e.key === "Enter") {
    e.preventDefault();
    const selected = flat.indexOf(searchHighlight.value);
    if (selected >= 0) {
      let count = 0;
      for (const g of searchResults.value) {
        for (const item of g.items) {
          if (count === selected) {
            goToResult(item);
            return;
          }
          count++;
        }
      }
    }
    // No highlight, go to first result
    if (searchResults.value.length && searchResults.value[0].items.length) {
      goToResult(searchResults.value[0].items[0]);
    }
  } else if (e.key === "Escape") {
    searchFocused.value = false;
    searchQuery.value = "";
  }
}

function goToResult(item) {
  searchFocused.value = false;
  searchQuery.value = "";
  navigateTo(item.to);
}

// Close search on click outside
watch(searchFocused, (val) => {
  if (val) {
    setTimeout(() => {
      const handler = (e) => {
        if (searchRef.value && !searchRef.value.contains(e.target)) {
          searchFocused.value = false;
          document.removeEventListener("click", handler);
        }
      };
      document.addEventListener("click", handler);
    }, 0);
  }
});

// Counts
const pendingOrders = ref(12);
const complaintsStore = useComplaintsStore();
const registrationsStore = useRegistrationsStore();
const notificationsStore = useNotificationsStore();
const openDisputes = ref(3);
const openReports = ref(4);
const pendingComplaints = computed(
  () => complaintsStore.pendingHighPriority.length,
);
const pendingApprovals = computed(() => registrationsStore.pending.length);
const unreadNotifs = computed(() => notificationsStore.unreadCount);
const notifications = computed(() => notificationsStore.allNotifications);

onMounted(() => {
  registrationsStore.fetchPending();
  registrationsStore.fetchHistory();
  notificationsStore.fetchNotifications();
});

// Derive page title from route
const pageTitle = computed(() => {
  const map = {
    "/dashboard": "Dashboard",
    "/approvals": "Approvals",
    "/map": "Live Map",
    "/orders": "Orders",
    "/sellers": "Sellers",
    "/drivers": "Drivers",
    "/customers": "Customers",
    "/products": "Products",
    "/analytics": "Analytics",
    "/payouts": "Payouts",
    "/disputes": "Disputes",
    "/complaints": "Complaints",
    "/reports": "Reports",
    "/notifications": "Notifications",
    "/notices": "Notices",
    "/settings": "Settings",
    "/admin-users": "Admin Users",
    "/settings/payments": "Payment Settings",
    "/settings/locations": "Location Settings",
    "/banners": "Banners",
    "/promos": "Promos",
    "/banner-requests": "Banner Requests",
  };
  // Match dynamic routes like /sellers/123
  const base = "/" + route.path.split("/")[1];
  return map[route.path] || map[base] || "Admin";
});

const toggleNotifPanel = () => {
  showNotifPanel.value = !showNotifPanel.value;
  showUserMenu.value = false;
};

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value;
  showNotifPanel.value = false;
};

const handleLogout = async () => {
  showUserMenu.value = false;
  await auth.logout();
  router.push("/login");
};

// Close menus on route change
watch(
  () => route.path,
  () => {
    showUserMenu.value = false;
    showNotifPanel.value = false;
    sidebarOpen.value = false;
  },
);

// Click outside directive
const vClickOutside = {
  mounted(el, binding) {
    el._clickOutside = (e) => {
      if (!el.contains(e.target)) binding.value(e);
    };
    document.addEventListener("click", el._clickOutside);
  },
  unmounted(el) {
    document.removeEventListener("click", el._clickOutside);
  },
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap");

:global(html),
:global(body) {
  margin: 0;
  padding: 0;
  height: 100%;
  background: #f3f4f6;
  font-family: "Inter", sans-serif;
}
:global(#__nuxt) {
  height: 100%;
}
*,
*::before,
*::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}
:global(a) {
  text-decoration: none;
  color: inherit;
}

/* ── SHELL ─────────────────────────────────── */
.admin-shell {
  display: flex;
  height: 100vh;
  overflow: hidden;
  background: #f3f4f6;
  position: relative;
}

/* ── SIDEBAR ───────────────────────────────── */
.sidebar {
  width: 256px;
  min-width: 256px;
  height: 100vh;
  background: #fff;
  border-right: 1px solid #e5e7eb;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  z-index: 50;
  flex-shrink: 0;
  position: relative;
}

/* Logo */
.sidebar-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 16px 14px;
  border-bottom: 1px solid #f3f4f6;
  flex-shrink: 0;
}

.sidebar-logo-img {
  width: 95px;
  height: 95px;
  border-radius: 10px;
  object-fit: cover;
  flex-shrink: 0;
}

.sidebar-logo-text {
  display: flex;
  flex-direction: column;
  gap: 1px;
  overflow: hidden;
}

.sidebar-brand {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 15px;
  font-weight: 700;
  color: #111827;
  line-height: 1.2;
  white-space: nowrap;
}

.sidebar-brand-sub {
  font-size: 11px;
  color: #9ca3af;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  white-space: nowrap;
}

/* Nav */
.sidebar-nav {
  flex: 1;
  overflow-y: auto;
  padding: 16px 12px;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.sidebar-nav::-webkit-scrollbar {
  width: 4px;
}
.sidebar-nav::-webkit-scrollbar-track {
  background: transparent;
}
.sidebar-nav::-webkit-scrollbar-thumb {
  background: #e5e7eb;
  border-radius: 4px;
}

.nav-section-label {
  font-size: 10.5px;
  font-weight: 700;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 1px;
  padding: 14px 8px 6px;
  margin-top: 4px;
}

.nav-section-label:first-child {
  padding-top: 4px;
  margin-top: 0;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 10px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  color: #4b5563;
  cursor: pointer;
  transition: all 0.15s;
  position: relative;
  text-decoration: none;
}

.nav-item:hover {
  background: #f9fafb;
  color: #111827;
}

.nav-item.active {
  background: #f0fdf4;
  color: #059669;
  font-weight: 600;
}

.nav-item.active .nav-icon {
  color: #059669;
}

.nav-item.sub {
  padding: 6px 10px;
}
.nav-item.sub:hover {
  background: transparent;
  color: #059669;
}
.nav-item.sub.active {
  background: transparent;
  font-weight: 500;
}
.nav-item.sub.active::before {
  display: none;
}

.nav-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
  color: #9ca3af;
  transition: color 0.15s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.nav-icon svg {
  width: 18px;
  height: 18px;
}

.nav-item:hover .nav-icon {
  color: #374151;
}

.nav-label {
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Badges */
.nav-badge {
  flex-shrink: 0;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 10px;
  line-height: 1.4;
}

.nav-badge.count {
  background: #f0fdf4;
  color: #059669;
  border: 1px solid #bbf7d0;
}

.nav-badge.warn {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
}

.nav-badge.live {
  background: #f0fdf4;
  color: #059669;
  border: 1px solid #bbf7d0;
  animation: livePulse 2s ease-in-out infinite;
}

@keyframes livePulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.6;
  }
}

/* Active left border accent */
.nav-item.active::before {
  content: "";
  position: absolute;
  left: -12px;
  top: 6px;
  bottom: 6px;
  width: 3px;
  background: #059669;
  border-radius: 0 3px 3px 0;
}

/* Sidebar User Footer */
.sidebar-user {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 16px;
  border-top: 1px solid #f3f4f6;
  flex-shrink: 0;
  background: #fff;
}

.user-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: linear-gradient(135deg, #059669, #047857);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 700;
  color: white;
  flex-shrink: 0;
}

.user-info {
  flex: 1;
  overflow: hidden;
  min-width: 0;
}

.user-name {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #111827;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-role {
  display: block;
  font-size: 11px;
  color: #9ca3af;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-logout {
  width: 30px;
  height: 30px;
  border: none;
  background: none;
  cursor: pointer;
  color: #9ca3af;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  transition: all 0.15s;
  flex-shrink: 0;
  padding: 0;
}

.user-logout:hover {
  background: #fef2f2;
  color: #dc2626;
}
.user-logout svg {
  width: 16px;
  height: 16px;
}

/* Sidebar Collapse Button */
.sidebar-collapse-btn {
  position: absolute;
  top: 20px;
  left: 242px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border: 2px solid #059669;
  background: #fff;
  border-radius: 50%;
  cursor: pointer;
  color: #059669;
  transition: all 0.2s;
  z-index: 100;
  box-shadow: 0 2px 8px rgba(5, 150, 105, 0.2);
  padding: 0;
}
.sidebar-collapse-btn:hover {
  background: #059669;
  color: #fff;
  box-shadow: 0 3px 12px rgba(5, 150, 105, 0.35);
  transform: scale(1.05);
}
.sidebar-collapse-btn svg {
  width: 12px;
  height: 12px;
  transition: transform 0.25s ease;
}
.sidebar-collapse-btn svg.flipped {
  transform: rotate(180deg);
}
.sidebar.collapsed ~ .sidebar-collapse-btn {
  left: 106px;
}

/* ── SIDEBAR COLLAPSED ──────────────────────── */
.sidebar.collapsed {
  width: 120px;
  min-width: 120px;
}
.sidebar.collapsed .sidebar-logo {
  justify-content: center;
  padding: 14px 12px;
  gap: 0;
}
.sidebar.collapsed .sidebar-logo-text {
  display: none;
}
.sidebar.collapsed .sidebar-logo-img {
  width: 95px;
  height: 95px;
}
.sidebar.collapsed .sidebar-collapse-btn {
  right: -14px;
}
.sidebar.collapsed .nav-section-label {
  display: none;
}
.sidebar.collapsed .nav-item {
  justify-content: center;
  padding: 9px;
}
.sidebar.collapsed .nav-label {
  display: none;
}
.sidebar.collapsed .nav-badge {
  display: none;
}
.sidebar.collapsed .nav-item.sub {
  display: none;
}
.sidebar.collapsed .nav-item.active::before {
  left: -12px;
}
.sidebar.collapsed .sidebar-user {
  justify-content: center;
  padding: 14px 8px;
}
.sidebar.collapsed .user-info {
  display: none;
}
.sidebar.collapsed .user-logout {
  display: none;
}

/* ── MAIN AREA ─────────────────────────────── */
.main-area {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  min-width: 0;
}

/* ── TOPBAR ────────────────────────────────── */
.topbar {
  height: 64px;
  min-height: 64px;
  background: #fff;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 28px;
  gap: 24px;
  position: relative;
  z-index: 40;
  flex-shrink: 0;
}

.topbar-left {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
}

.topbar-title {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 18px;
  font-weight: 700;
  color: #111827;
  line-height: 1.2;
  white-space: nowrap;
}

.topbar-breadcrumb {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  color: #9ca3af;
}

.topbar-breadcrumb svg {
  width: 12px;
  height: 12px;
}

.breadcrumb-current {
  color: #6b7280;
  font-weight: 500;
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
  position: relative;
}

/* Search */
.topbar-search {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f9fafb;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  padding: 8px 12px;
  min-width: 240px;
  transition: border-color 0.2s;
  position: relative;
}

.topbar-search:focus-within {
  border-color: #059669;
  background: #fff;
}

.topbar-search svg {
  width: 16px;
  height: 16px;
  color: #9ca3af;
  flex-shrink: 0;
}

.search-input {
  flex: 1;
  border: none;
  background: transparent;
  font-size: 13px;
  font-family: "Inter", sans-serif;
  color: #111827;
  outline: none;
  min-width: 0;
}

.search-input::placeholder {
  color: #9ca3af;
}

.search-kbd {
  font-size: 10px;
  color: #9ca3af;
  background: #e5e7eb;
  border-radius: 4px;
  padding: 2px 6px;
  font-family: "Inter", sans-serif;
  flex-shrink: 0;
}

/* Search dropdown */
.search-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  margin-top: 6px;
  background: #fff;
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
  max-height: 400px;
  overflow-y: auto;
  z-index: 200;
  padding: 8px 0;
}
.search-empty {
  padding: 24px 16px;
  text-align: center;
  font-size: 13px;
  color: #9ca3af;
}
.search-group-label {
  padding: 6px 14px 4px;
  font-size: 10px;
  font-weight: 700;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.8px;
}
.search-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 14px;
  cursor: pointer;
  transition: background 0.1s;
}
.search-item:hover,
.search-item.highlighted {
  background: #f0fdf4;
}
.search-item-icon {
  font-size: 16px;
  flex-shrink: 0;
}
.search-item-body {
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.search-item-title {
  font-size: 13px;
  font-weight: 600;
  color: #111827;
}
.search-item-sub {
  font-size: 11px;
  color: #9ca3af;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Topbar buttons */
.topbar-btn {
  position: relative;
  width: 38px;
  height: 38px;
  border: 1.5px solid #e5e7eb;
  background: #fff;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
  transition: all 0.15s;
}

.topbar-btn:hover {
  border-color: #059669;
  color: #059669;
  background: #f0fdf4;
}

.topbar-btn svg {
  width: 18px;
  height: 18px;
}

.btn-dot {
  position: absolute;
  top: -4px;
  right: -4px;
  min-width: 16px;
  height: 16px;
  background: #dc2626;
  border-radius: 8px;
  font-size: 9px;
  font-weight: 700;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 4px;
  border: 2px solid white;
}

.topbar-avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: linear-gradient(135deg, #059669, #047857);
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 700;
  color: white;
  transition: box-shadow 0.15s;
  font-family: "Inter", sans-serif;
}

.topbar-avatar:hover {
  box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.2);
}

/* User Dropdown */
.user-dropdown {
  position: absolute;
  top: calc(100% + 10px);
  right: 0;
  width: 220px;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
  overflow: hidden;
  z-index: 100;
  animation: dropIn 0.15s ease;
}

@keyframes dropIn {
  from {
    opacity: 0;
    transform: translateY(-6px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.dropdown-header {
  padding: 14px 16px;
  background: #f9fafb;
}

.dropdown-header strong {
  display: block;
  font-size: 13px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 2px;
}

.dropdown-header span {
  font-size: 12px;
  color: #9ca3af;
}

.dropdown-divider {
  height: 1px;
  background: #f3f4f6;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 11px 16px;
  font-size: 13px;
  font-weight: 500;
  color: #374151;
  cursor: pointer;
  transition: background 0.15s;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  font-family: "Inter", sans-serif;
  text-decoration: none;
}

.dropdown-item:hover {
  background: #f9fafb;
}
.dropdown-item.danger {
  color: #dc2626;
}
.dropdown-item.danger:hover {
  background: #fef2f2;
}
.dropdown-item svg {
  width: 15px;
  height: 15px;
  flex-shrink: 0;
}

/* ── PAGE CONTENT ──────────────────────────── */
.page-content {
  flex: 1;
  overflow-y: auto;
  padding: 28px;
  background: #f3f4f6;
}

.page-content::-webkit-scrollbar {
  width: 6px;
}
.page-content::-webkit-scrollbar-track {
  background: transparent;
}
.page-content::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 4px;
}

/* ── NOTIFICATION PANEL ───────────────────── */
.notif-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.15);
  z-index: 90;
  backdrop-filter: blur(1px);
}

.notif-panel {
  position: fixed;
  top: 0;
  right: 0;
  width: 360px;
  height: 100vh;
  background: #fff;
  border-left: 1px solid #e5e7eb;
  z-index: 100;
  display: flex;
  flex-direction: column;
  transform: translateX(100%);
  transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: -8px 0 32px rgba(0, 0, 0, 0.08);
}

.notif-panel.open {
  transform: translateX(0);
}

.notif-panel-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 20px 16px;
  border-bottom: 1px solid #f3f4f6;
  flex-shrink: 0;
}

.notif-panel-header h3 {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 16px;
  font-weight: 700;
  color: #111827;
}

.notif-panel-header button {
  width: 30px;
  height: 30px;
  border: none;
  background: none;
  cursor: pointer;
  color: #9ca3af;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  transition: all 0.15s;
}

.notif-panel-header button:hover {
  background: #f3f4f6;
  color: #374151;
}
.notif-panel-header button svg {
  width: 18px;
  height: 18px;
}

.notif-list {
  flex: 1;
  overflow-y: auto;
  padding: 8px 0;
}

.notif-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 14px 20px;
  border-bottom: 1px solid #f9fafb;
  transition: background 0.15s;
  cursor: pointer;
}

.notif-item:hover {
  background: #f9fafb;
}
.notif-item.unread {
  background: #f0fdf4;
}
.notif-item.unread:hover {
  background: #dcfce7;
}

.notif-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  flex-shrink: 0;
  margin-top: 4px;
}

.notif-dot.order {
  background: #059669;
}
.notif-dot.driver {
  background: #3b82f6;
}
.notif-dot.dispute {
  background: #dc2626;
}
.notif-dot.seller {
  background: #f59e0b;
}
.notif-dot.system {
  background: #9ca3af;
}

.notif-body {
  flex: 1;
  min-width: 0;
}
.notif-text {
  font-size: 13px;
  color: #374151;
  font-weight: 500;
  line-height: 1.45;
  margin-bottom: 4px;
}
.notif-time {
  font-size: 11px;
  color: #9ca3af;
}

/* ── HAMBURGER ─────────────────────────────── */
.hamburger-btn {
  display: none;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  border: 1.5px solid #e5e7eb;
  background: #fff;
  border-radius: 8px;
  cursor: pointer;
  color: #6b7280;
  flex-shrink: 0;
  transition: all 0.15s;
}
.hamburger-btn:hover {
  border-color: #059669;
  color: #059669;
}
.hamburger-btn svg {
  width: 18px;
  height: 18px;
}

/* Sidebar overlay (mobile) */
.sidebar-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  z-index: 45;
  opacity: 0;
  transition: opacity 0.25s;
  pointer-events: none;
}
.sidebar-overlay.open {
  opacity: 1;
  pointer-events: auto;
}

/* ── RESPONSIVE ────────────────────────────── */
@media (max-width: 1024px) {
  .sidebar.collapsed {
    width: 256px;
    min-width: 256px;
  }
  .sidebar.collapsed .sidebar-logo {
    justify-content: flex-start;
    padding: 16px 14px;
    gap: 10px;
  }
  .sidebar.collapsed .sidebar-logo-text {
    display: flex;
  }
  .sidebar.collapsed .sidebar-logo-img {
    width: 95px;
    height: 95px;
  }
  .sidebar.collapsed .nav-section-label {
    display: block;
  }
  .sidebar.collapsed .nav-item {
    justify-content: flex-start;
    padding: 9px 10px;
  }
  .sidebar.collapsed .nav-label {
    display: block;
  }
  .sidebar.collapsed .nav-badge {
    display: inline;
  }
  .sidebar.collapsed .nav-item.sub {
    display: flex;
  }
  .sidebar.collapsed .sidebar-user {
    justify-content: flex-start;
    padding: 14px 16px;
  }
  .sidebar.collapsed .user-info {
    display: block;
  }
  .sidebar.collapsed .user-logout {
    display: flex;
  }
  .sidebar-collapse-btn {
    display: none;
  }
  .hamburger-btn {
    display: flex;
  }
  .sidebar {
    position: fixed;
    left: -256px;
    top: 0;
    bottom: 0;
    transition: left 0.3s ease;
    z-index: 50;
  }
  .sidebar.open {
    left: 0;
  }
  .sidebar-overlay {
    display: block;
  }
  .main-area {
    margin-left: 0;
    width: 100%;
  }
  .topbar {
    padding: 0 16px;
  }
  .topbar-search {
    min-width: 0;
  }
  .search-kbd {
    display: none;
  }
}

@media (max-width: 768px) {
  .topbar-title {
    font-size: 15px;
  }
  .topbar-breadcrumb {
    display: none;
  }
  .topbar-search {
    display: none;
  }
  .page-content {
    padding: 16px;
  }
}

@media (max-width: 480px) {
  .topbar-right {
    gap: 4px;
  }
  .topbar-btn {
    width: 32px;
    height: 32px;
  }
  .topbar-avatar {
    width: 32px;
    height: 32px;
  }
  .page-content {
    padding: 12px;
  }
}

@media (max-height: 620px) {
  .sidebar-nav {
    overflow-y: auto;
  }
  .sidebar-user {
    padding: 10px 14px;
  }
  .sidebar-user .user-avatar {
    width: 32px;
    height: 32px;
    font-size: 13px;
  }
  .sidebar-user .user-role {
    font-size: 10px;
  }
  .topbar {
    padding: 0 16px;
    height: 48px;
    min-height: 48px;
  }
  .topbar-title {
    font-size: 15px;
  }
  .topbar-breadcrumb {
    display: none;
  }
  .topbar-search {
    min-width: 0;
    flex: 1;
    max-width: 240px;
  }
  .search-kbd {
    display: none;
  }
  .topbar-btn {
    width: 32px;
    height: 32px;
  }
  .topbar-avatar {
    width: 30px;
    height: 30px;
  }
  .page-content {
    padding: 12px 16px;
  }
  .page-shell {
    padding-top: 0;
  }
  .page-toolbar {
    padding: 10px 16px;
  }
  .table-card {
    padding: 12px;
  }
  .data-table th,
  .data-table td {
    padding: 8px 10px;
    font-size: 12px;
  }
}
</style>

<style>
/* ── DARK MODE ──────────────────────────── */
:root.dark-mode,
html.dark-mode {
  color-scheme: dark;
}

.dark-mode .admin-shell {
  background: #1a1a2e;
}
.dark-mode .sidebar {
  background: #16213e;
  border-right-color: #1e2a4a;
}
.dark-mode .sidebar-logo {
  border-bottom-color: #1e2a4a;
}
.dark-mode .sidebar-logo-img {
  background: #f0fdf4;
  border-radius: 10px;
  padding: 4px;
  filter: brightness(1.1);
}
.dark-mode .sidebar-brand {
  color: #e2e8f0;
}
.dark-mode .sidebar-brand-sub {
  color: #64748b;
}
.dark-mode .nav-section-label {
  color: #475569;
}
.dark-mode .nav-item {
  color: #94a3b8;
}
.dark-mode .nav-item:hover {
  background: #1e2a4a;
  color: #e2e8f0;
}
.dark-mode .nav-item.active {
  background: rgba(5, 150, 105, 0.15);
  color: #34d399;
}
.dark-mode .nav-item.active .nav-icon {
  color: #34d399;
}
.dark-mode .nav-item.active::before {
  background: #34d399;
}
.dark-mode .nav-icon {
  color: #475569;
}
.dark-mode .nav-item:hover .nav-icon {
  color: #94a3b8;
}
.dark-mode .nav-badge.count {
  background: rgba(5, 150, 105, 0.15);
  color: #34d399;
  border-color: rgba(5, 150, 105, 0.3);
}

.dark-mode .main-area {
  background: #1a1a2e;
}
.dark-mode .topbar {
  background: #16213e;
  border-bottom-color: #1e2a4a;
}
.dark-mode .topbar-title {
  color: #e2e8f0;
}
.dark-mode .topbar-breadcrumb {
  color: #64748b;
}
.dark-mode .breadcrumb-current {
  color: #94a3b8;
}
.dark-mode .topbar-search {
  background: #1e2a4a;
  border-color: #2a3a5a;
}
.dark-mode .topbar-search:focus-within {
  border-color: #059669;
}
.dark-mode .topbar-search input {
  color: #e2e8f0;
}
.dark-mode .topbar-search svg {
  color: #475569;
}
.dark-mode .topbar-btn {
  color: #94a3b8;
}
.dark-mode .topbar-btn:hover {
  background: #1e2a4a;
  color: #e2e8f0;
}
.dark-mode .topbar-avatar {
  border-color: #2a3a5a;
}
.dark-mode .result-count {
  color: #64748b;
}

.dark-mode .user-dropdown {
  background: #16213e;
  border-color: #1e2a4a;
}
.dark-mode .dropdown-header strong {
  color: #e2e8f0;
}
.dark-mode .dropdown-header span {
  color: #64748b;
}
.dark-mode .dropdown-divider {
  border-color: #1e2a4a;
}
.dark-mode .dropdown-item {
  color: #94a3b8;
}
.dark-mode .dropdown-item:hover {
  background: #1e2a4a;
  color: #e2e8f0;
}
.dark-mode .dropdown-item.danger:hover {
  background: rgba(220, 38, 38, 0.1);
  color: #f87171;
}

.dark-mode .page-content {
  background: #1a1a2e;
}
.dark-mode .page-content::-webkit-scrollbar-thumb {
  background: #2a3a5a;
}
.dark-mode .page-content::-webkit-scrollbar-track {
  background: transparent;
}

.dark-mode .search-dropdown {
  background: #16213e;
  border-color: #1e2a4a;
}
.dark-mode .search-group-label {
  color: #64748b;
}
.dark-mode .search-item {
  color: #94a3b8;
}
.dark-mode .search-item:hover {
  background: #1e2a4a;
  color: #e2e8f0;
}
.dark-mode .search-item-sub {
  color: #64748b;
}

.dark-mode .notif-panel {
  background: #16213e;
  border-left-color: #1e2a4a;
}
.dark-mode .notif-panel-header h3 {
  color: #e2e8f0;
}
.dark-mode .notif-panel-header button {
  color: #64748b;
}
.dark-mode .notif-panel-header button:hover {
  background: #1e2a4a;
  color: #e2e8f0;
}
.dark-mode .notif-item {
  border-bottom-color: #1e2a4a;
}
.dark-mode .notif-text {
  color: #cbd5e1;
}
.dark-mode .notif-time {
  color: #64748b;
}

/* Shared page component overrides */
.dark-mode .page-shell {
  color: #e2e8f0;
}
.dark-mode .page-toolbar {
  color: #e2e8f0;
}

.dark-mode .table-card {
  background: #16213e;
  border-color: #1e2a4a;
}
.dark-mode .data-table th {
  color: #64748b;
  border-bottom-color: #1e2a4a;
}
.dark-mode .data-table td {
  color: #cbd5e1;
  border-bottom-color: #1a1a2e;
}
.dark-mode .id-cell {
  color: #34d399;
}
.dark-mode .name-cell {
  color: #e2e8f0;
}
.dark-mode .date-cell {
  color: #64748b;
}
.dark-mode .amount-cell {
  color: #e2e8f0;
}
.dark-mode .action-btn {
  border-color: #2a3a5a;
  color: #64748b;
}
.dark-mode .action-btn:hover {
  border-color: #059669;
  color: #34d399;
}

.dark-mode .search-box {
  background: #1e2a4a;
  border-color: #2a3a5a;
}
.dark-mode .search-box:focus-within {
  border-color: #059669;
}
.dark-mode .search-box input {
  color: #e2e8f0;
}
.dark-mode .search-box svg {
  color: #475569;
}
.dark-mode .filter-select {
  background: #1e2a4a;
  border-color: #2a3a5a;
  color: #cbd5e1;
}
.dark-mode .filter-select:focus {
  border-color: #059669;
}

.dark-mode .method-badge.cod {
  background: #1e2a4a;
  color: #94a3b8;
}
.dark-mode .method-badge.gcash {
  background: rgba(5, 150, 105, 0.15);
  color: #34d399;
}
.dark-mode .status-badge.delivered {
  background: rgba(5, 150, 105, 0.15);
  color: #34d399;
}
.dark-mode .status-badge.transit {
  background: rgba(59, 130, 246, 0.15);
  color: #60a5fa;
}
.dark-mode .status-badge.preparing {
  background: rgba(217, 119, 6, 0.15);
  color: #fbbf24;
}
.dark-mode .status-badge.pending {
  background: #1e2a4a;
  color: #94a3b8;
}
.dark-mode .status-badge.cancelled {
  background: rgba(220, 38, 38, 0.15);
  color: #f87171;
}
.dark-mode .status-badge.completed {
  background: rgba(5, 150, 105, 0.15);
  color: #34d399;
}
.dark-mode .status-badge.open {
  background: rgba(220, 38, 38, 0.15);
  color: #f87171;
}
.dark-mode .status-badge.investigating {
  background: rgba(217, 119, 6, 0.15);
  color: #fbbf24;
}
.dark-mode .status-badge.resolved {
  background: rgba(5, 150, 105, 0.15);
  color: #34d399;
}
.dark-mode .status-badge.failed {
  background: rgba(220, 38, 38, 0.15);
  color: #f87171;
}

.dark-mode .priority-badge.high {
  background: rgba(220, 38, 38, 0.15);
  color: #f87171;
}
.dark-mode .priority-badge.medium {
  background: rgba(217, 119, 6, 0.15);
  color: #fbbf24;
}
.dark-mode .priority-badge.low {
  background: #1e2a4a;
  color: #94a3b8;
}

.dark-mode .stats-grid .stat-card {
  background: #16213e;
  border-color: #1e2a4a;
}
.dark-mode .stat-label {
  color: #64748b;
}
.dark-mode .stat-value {
  color: #e2e8f0;
}
.dark-mode .stat-period {
  color: #475569;
}
.dark-mode .stat-change.up {
  color: #34d399;
}
.dark-mode .stat-change.down {
  color: #f87171;
}

.dark-mode .dash-card {
  background: #16213e;
  border-color: #1e2a4a;
}
.dark-mode .card-head h3 {
  color: #e2e8f0;
}
.dark-mode .card-badge {
  background: #1e2a4a;
  color: #64748b;
}

.dark-mode .modal-overlay {
  background: rgba(0, 0, 0, 0.6);
}
.dark-mode .modal-card {
  background: #16213e;
}
.dark-mode .modal-top {
  border-bottom-color: #1e2a4a;
}
.dark-mode .modal-title {
  color: #e2e8f0;
}
.dark-mode .modal-close {
  color: #64748b;
}
.dark-mode .modal-close:hover {
  background: #1e2a4a;
  color: #e2e8f0;
}
.dark-mode .modal-body {
  color: #cbd5e1;
}
.dark-mode .dl {
  color: #64748b;
}
.dark-mode .dv {
  color: #e2e8f0;
}
.dark-mode .modal-actions-single,
.dark-mode .modal-actions {
  border-top-color: #1e2a4a;
}
.dark-mode .modal-btn.secondary {
  background: #1e2a4a;
  color: #94a3b8;
}
.dark-mode .modal-btn.secondary:hover {
  background: #2a3a5a;
}

.dark-mode .settings-subnav {
  background: #16213e;
  border-color: #1e2a4a;
}
.dark-mode .subnav-item {
  color: #64748b;
}
.dark-mode .subnav-item:hover {
  color: #cbd5e1;
  background: #1e2a4a;
}
.dark-mode .subnav-item.active {
  background: rgba(5, 150, 105, 0.15);
  color: #34d399;
}
.dark-mode .settings-section {
  background: #16213e;
  border-color: #1e2a4a;
}
.dark-mode .settings-section h3 {
  color: #e2e8f0;
}
.dark-mode .form-group label {
  color: #cbd5e1;
}
.dark-mode .form-input {
  background: #1e2a4a;
  border-color: #2a3a5a;
  color: #e2e8f0;
}
.dark-mode .form-input:focus {
  border-color: #059669;
}
.dark-mode .form-input:disabled {
  background: #1a1a2e;
  color: #64748b;
}
.dark-mode .form-hint {
  color: #475569;
}
.dark-mode .pref-row {
  border-bottom-color: #1a1a2e;
}
.dark-mode .pref-label {
  color: #e2e8f0;
}
.dark-mode .pref-desc {
  color: #64748b;
}
.dark-mode .toggle-slider {
  background: #2a3a5a;
}
.dark-mode .toggle input:checked + .toggle-slider {
  background: #059669;
}
.dark-mode .save-btn {
  background: #059669;
}
.dark-mode .save-btn:hover {
  background: #047857;
}
.dark-mode .reset-btn {
  background: #1e2a4a;
  color: #94a3b8;
  border-color: #2a3a5a;
}
.dark-mode .reset-btn:hover {
  border-color: #64748b;
}

.dark-mode .example-box {
  background: #1e2a4a;
}
.dark-mode .example-intro {
  color: #94a3b8;
}
.dark-mode .example-row {
  color: #cbd5e1;
  border-bottom-color: #1e2a4a;
}
.dark-mode .example-row.highlight {
  color: #34d399;
}
.dark-mode .preview-card {
  background: #1e2a4a;
}
.dark-mode .preview-label {
  color: #64748b;
}
.dark-mode .preview-value {
  color: #e2e8f0;
}

.dark-mode .detail-grid {
  background: #1e2a4a;
  border-color: #1e2a4a;
}
.dark-mode .detail-item .dl {
  color: #64748b;
}
.dark-mode .detail-item .dv {
  color: #e2e8f0;
}

.dark-mode .sidebar-user {
  border-top-color: #1e2a4a;
}
.dark-mode .user-name {
  color: #e2e8f0;
}
.dark-mode .user-role {
  color: #64748b;
}
.dark-mode .user-avatar {
  background: #1e2a4a;
  color: #34d399;
}
.dark-mode .user-logout {
  color: #64748b;
}
.dark-mode .user-logout:hover {
  background: rgba(220, 38, 38, 0.1);
  color: #f87171;
}

/* Disputes analytics */
.dark-mode .tab-switch {
  background: #1e2a4a;
}
.dark-mode .tab-btn {
  color: #64748b;
}
.dark-mode .tab-btn.active {
  background: #16213e;
  color: #34d399;
}
.dark-mode .stat-card {
  background: #16213e;
  border-color: #1e2a4a;
}
.dark-mode .stat-value {
  color: #e2e8f0;
}
.dark-mode .chart-card {
  background: #16213e;
  border-color: #1e2a4a;
}
.dark-mode .chart-card h4 {
  color: #e2e8f0;
}
.dark-mode .chart-bar-bg {
  background: #1e2a4a;
}
.dark-mode .chart-bar-val {
  color: #e2e8f0;
}
.dark-mode .reason-item {
  border-bottom-color: #1e2a4a;
}
.dark-mode .reason-name {
  color: #cbd5e1;
}
.dark-mode .reason-count {
  color: #34d399;
}

/* Categories */
.dark-mode .categories-grid .category-card {
  background: #16213e;
  border-color: #1e2a4a;
}
.dark-mode .cat-name {
  color: #e2e8f0;
}
.dark-mode .cat-count {
  color: #64748b;
}
.dark-mode .cat-bar-bg {
  background: #1e2a4a;
}
.dark-mode .icon-btn {
  background: #1e2a4a;
  border-color: #2a3a5a;
  color: #64748b;
}
.dark-mode .icon-btn:hover {
  border-color: #059669;
  color: #34d399;
}
.dark-mode .icon-btn.danger:hover {
  border-color: #dc2626;
  color: #f87171;
}
.dark-mode .modal-header h3 {
  color: #e2e8f0;
}
.dark-mode .modal-form .form-group label {
  color: #cbd5e1;
}
.dark-mode .color-swatch {
  border-color: #2a3a5a;
}
.dark-mode .color-swatch.active {
  border-color: #34d399;
}
.dark-mode .modal-btn.danger {
  background: #dc2626;
  color: #fff;
}
.dark-mode .modal-btn.danger:hover {
  background: #b91c1c;
}
.dark-mode .modal-btn.success {
  background: #059669;
  color: #fff;
}
.dark-mode .modal-btn.success:hover {
  background: #047857;
}
.dark-mode .modal-btn.primary {
  background: #3b82f6;
  color: #fff;
}
.dark-mode .modal-btn.primary:hover {
  background: #2563eb;
}

/* Notifications */
.dark-mode .notif-item {
  border-bottom-color: #1e2a4a;
}
.dark-mode .notif-item:hover {
  background: #1e2a4a;
}
.dark-mode .notif-item.unread {
  background: rgba(5, 150, 105, 0.1);
}
.dark-mode .notif-text {
  color: #cbd5e1;
}
.dark-mode .notif-item.unread .notif-text {
  color: #e2e8f0;
}
.dark-mode .notif-time {
  color: #64748b;
}
.dark-mode .notif-delete {
  color: #475569;
}
.dark-mode .notif-delete:hover {
  background: rgba(220, 38, 38, 0.1);
  color: #f87171;
}
.dark-mode .mark-read-btn {
  background: #1e2a4a;
  border-color: #2a3a5a;
}
.dark-mode .mark-read-btn:hover {
  background: rgba(5, 150, 105, 0.15);
  border-color: #059669;
}
.dark-mode .pagination-wrap {
  border-top-color: #1e2a4a;
}

/* Banners */
.dark-mode .banner-card {
  background: #16213e;
  border-color: #1e2a4a;
}
.dark-mode .banner-title {
  color: #e2e8f0;
}
.dark-mode .banner-subtitle {
  color: #94a3b8;
}
.dark-mode .banner-link {
  color: #34d399;
}
.dark-mode .status-badge.active {
  background: rgba(5, 150, 105, 0.15);
  color: #34d399;
}
.dark-mode .status-badge.inactive {
  background: #1e2a4a;
  color: #64748b;
}
.dark-mode .promo-code {
  color: #34d399;
}

/* Banner Requests */
.dark-mode .status-badge.pending {
  background: rgba(217, 119, 6, 0.15);
  color: #fbbf24;
}
.dark-mode .status-badge.approved {
  background: rgba(5, 150, 105, 0.15);
  color: #34d399;
}
.dark-mode .status-badge.rejected {
  background: rgba(220, 38, 38, 0.15);
  color: #f87171;
}

/* Modal danger/success/primary btn dark mode */
.dark-mode .modal-btn.danger {
  background: #dc2626;
  color: #fff;
}
.dark-mode .modal-btn.danger:hover {
  background: #b91c1c;
}
.dark-mode .modal-btn.success {
  background: #059669;
  color: #fff;
}
.dark-mode .modal-btn.success:hover {
  background: #047857;
}
.dark-mode .modal-btn.primary {
  background: #3b82f6;
  color: #fff;
}
.dark-mode .modal-btn.primary:hover {
  background: #2563eb;
}

/* Payment Settings */
.dark-mode .pm-name {
  color: #e2e8f0;
}
.dark-mode .payment-method-card {
  background: #16213e;
  border-color: #1e2a4a;
}
.dark-mode .payment-method-card:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}
.dark-mode .toggle-text {
  color: #94a3b8;
}
.dark-mode .toggle-slider {
  background: #2a3a5a;
}
.dark-mode .toggle-label input:checked + .toggle-slider {
  background: #059669;
}
.dark-mode .preview-chip {
  background: rgba(5, 150, 105, 0.15) !important;
  border-color: rgba(5, 150, 105, 0.3) !important;
  color: #34d399 !important;
}
</style>
