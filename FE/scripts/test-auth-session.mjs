import assert from "node:assert/strict";
import {
  buildAuthHeaders,
  clearAuthSession,
  getPreferredLocale,
  inferAuthType,
  readAuthSession,
  saveAuthSession,
  setPreferredLocale,
} from "../src/services/authSession.js";

function createMemoryStorage() {
  const store = new Map();

  return {
    getItem(key) {
      return store.has(key) ? store.get(key) : null;
    },
    setItem(key, value) {
      store.set(key, String(value));
    },
    removeItem(key) {
      store.delete(key);
    },
  };
}

const storage = createMemoryStorage();

assert.equal(inferAuthType({ Ma_khach_hang: "KH000001" }), "customer");
assert.equal(inferAuthType({ Ma_doi_tac: "DT000001" }), "partner");
assert.equal(inferAuthType({ Ma_quan_tri: "AD000001" }), "admin");
assert.equal(inferAuthType({}, "ADMIN"), "admin");

saveAuthSession(
  {
    token: "demo-token",
    authType: "customer",
    user: { Ma_khach_hang: "KH000001", Ho_va_ten: "Khách hàng A" },
  },
  storage,
);

assert.deepEqual(readAuthSession(storage), {
  token: "demo-token",
  authType: "customer",
  user: { Ma_khach_hang: "KH000001", Ho_va_ten: "Khách hàng A" },
  isAuthenticated: true,
});

assert.deepEqual(buildAuthHeaders(true, storage), {
  Accept: "application/json",
  "Content-Type": "application/json",
  Authorization: "Bearer demo-token",
  "X-Auth-Type": "customer",
});

setPreferredLocale("en", storage);
assert.equal(getPreferredLocale(storage), "en");

clearAuthSession(storage);
assert.deepEqual(readAuthSession(storage), {
  token: "",
  authType: "",
  user: null,
  isAuthenticated: false,
});

console.log("OK: auth session helpers passed.");
