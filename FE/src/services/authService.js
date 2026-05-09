import { goiApi } from "./httpClient";
import authStorage from "./authStorage";

/**
 * Authentication service to handle API calls related to user sessions.
 */
export const authService = {
  /**
   * Logs out the customer by calling the backend and clearing local storage.
   */
  async logoutCustomer() {
    try {
      // Send request to backend to delete the token
      // Route::middleware('auth:sanctum')->post('/khach-hang/logout', ...)
      await goiApi("/khach-hang/logout", {
        method: "POST",
      });
    } catch (error) {
      // We log the error but proceed with local cleanup anyway
      console.error("Backend logout failed:", error);
    } finally {
      // Clear the session from authStorage
      authStorage.clearSession(authStorage.Role.CUSTOMER);
      
      // Clean up any legacy or shared keys
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      localStorage.removeItem("auth_type");
      
      // Force a redirect to the home page or login page
      // Using window.location.href to ensure a clean state
      window.location.href = "/";
    }
  },

  /**
   * Logs out the admin by calling the backend and clearing local storage.
   */
  async logoutAdmin() {
    try {
      await goiApi("/admin/logout", {
        method: "POST",
      });
    } catch (error) {
      console.error("Admin logout API failed:", error);
    } finally {
      authStorage.clearSession(authStorage.Role.ADMIN);
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      localStorage.removeItem("auth_type");
      window.location.href = "/admin/dang-nhap";
    }
  },

  /**
   * Logs out the partner by calling the backend and clearing local storage.
   */
  async logoutPartner() {
    try {
      await goiApi("/doi-tac/logout", {
        method: "POST",
      });
    } catch (error) {
      console.error("Partner logout API failed:", error);
    } finally {
      authStorage.clearSession(authStorage.Role.PARTNER);
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      localStorage.removeItem("auth_type");
      window.location.href = "/doi-tac/dang-nhap";
    }
  },

  /**
   * General logout for any role
   */
  async logout(role) {
    if (role === authStorage.Role.CUSTOMER) {
      return this.logoutCustomer();
    }
    if (role === authStorage.Role.ADMIN) {
      return this.logoutAdmin();
    }
    if (role === authStorage.Role.PARTNER) {
      return this.logoutPartner();
    }
    
    authStorage.clearSession(role);
    window.location.href = "/";
  }
};

export default authService;
