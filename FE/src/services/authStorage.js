/**
 * Utility to manage role-based authentication storage.
 * This allows multiple roles to be logged in simultaneously in different tabs.
 */

export const Role = {
  ADMIN: "admin",
  CUSTOMER: "customer",
  PARTNER: "partner",
};

const Keys = {
  [Role.ADMIN]: {
    token: "admin_token",
    user: "admin_user",
    type: "admin",
  },
  [Role.CUSTOMER]: {
    token: "customer_token",
    user: "customer_user",
    type: "customer",
  },
  [Role.PARTNER]: {
    token: "partner_token",
    user: "partner_user",
    type: "partner",
  },
};

/**
 * Determines the current role context based on the URL path.
 */
export function getCurrentRoleFromPath(path = window.location.pathname) {
  const lowerPath = path.toLowerCase();
  if (lowerPath.startsWith("/admin") || lowerPath.startsWith("/dashboard")) {
    return Role.ADMIN;
  }
  if (lowerPath.startsWith("/doi-tac")) {
    return Role.PARTNER;
  }
  if (lowerPath.startsWith("/khach-hang")) {
    return Role.CUSTOMER;
  }
  return Role.CUSTOMER; // Default fallback
}

export function getToken(role) {
  const targetRole = role || getCurrentRoleFromPath();
  return localStorage.getItem(Keys[targetRole]?.token) || "";
}

export function getUser(role) {
  const targetRole = role || getCurrentRoleFromPath();
  try {
    const raw = localStorage.getItem(Keys[targetRole]?.user);
    return raw ? JSON.parse(raw) : null;
  } catch {
    return null;
  }
}

export function saveSession(role, { token, user }) {
  if (!role || !Keys[role]) return;
  
  if (token) {
    localStorage.setItem(Keys[role].token, token);
  }
  if (user) {
    localStorage.setItem(Keys[role].user, JSON.stringify(user));
  }
  localStorage.setItem(`${role}_auth_type`, role);
  
  // Trigger storage event for tab synchronization if needed
  window.dispatchEvent(new Event("storage"));
}

export function clearSession(role) {
  const targetRole = role || getCurrentRoleFromPath();
  if (!Keys[targetRole]) return;

  localStorage.removeItem(Keys[targetRole].token);
  localStorage.removeItem(Keys[targetRole].user);
  localStorage.removeItem(`${targetRole}_auth_type`);
  
  window.dispatchEvent(new Event("storage"));
}

/**
 * Compatibility helper to get any active session 
 * (used for general checks if specific role doesn't matter)
 */
export function getAnyToken() {
  return getToken(Role.ADMIN) || getToken(Role.PARTNER) || getToken(Role.CUSTOMER);
}

export default {
  Role,
  getCurrentRoleFromPath,
  getToken,
  getUser,
  saveSession,
  clearSession,
  getAnyToken,
};
