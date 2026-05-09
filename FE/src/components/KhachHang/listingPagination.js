function toPositiveInteger(value, fallback) {
  const parsed = Number.parseInt(String(value ?? ""), 10);
  if (!Number.isFinite(parsed) || parsed <= 0) {
    return fallback;
  }

  return parsed;
}

export function resetListingPage() {
  return 1;
}

export function buildPaginatedListing(items = [], { currentPage = 1, perPage = 8 } = {}) {
  const safeItems = Array.isArray(items) ? items : [];
  const safePerPage = toPositiveInteger(perPage, 8);
  const totalItems = safeItems.length;
  const totalPages = Math.max(1, Math.ceil(totalItems / safePerPage));
  const normalizedCurrentPage = Math.min(
    Math.max(toPositiveInteger(currentPage, 1), 1),
    totalPages,
  );

  const startIndex = (normalizedCurrentPage - 1) * safePerPage;

  return {
    items: safeItems.slice(startIndex, startIndex + safePerPage),
    currentPage: normalizedCurrentPage,
    totalItems,
    totalPages,
    perPage: safePerPage,
    hasPreviousPage: normalizedCurrentPage > 1,
    hasNextPage: normalizedCurrentPage < totalPages,
  };
}

export function buildPaginationWindow(totalPages, currentPage, maxButtons = 5) {
  const safeTotalPages = toPositiveInteger(totalPages, 1);
  const safeCurrentPage = Math.min(Math.max(toPositiveInteger(currentPage, 1), 1), safeTotalPages);
  const safeMaxButtons = Math.max(toPositiveInteger(maxButtons, 5), 3);

  if (safeTotalPages <= safeMaxButtons) {
    return Array.from({ length: safeTotalPages }, (_, index) => index + 1);
  }

  const halfWindow = Math.floor(safeMaxButtons / 2);
  let start = Math.max(1, safeCurrentPage - halfWindow);
  let end = start + safeMaxButtons - 1;

  if (end > safeTotalPages) {
    end = safeTotalPages;
    start = Math.max(1, end - safeMaxButtons + 1);
  }

  return Array.from({ length: end - start + 1 }, (_, index) => start + index);
}
