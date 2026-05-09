/**
 * Utility to fix broken placeholder image URLs and handle Unsplash Source deprecation.
 */

export const HINH_ANH_MAC_DINH = "https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=1200&q=80";

/**
 * Normalizes an image URL, replacing broken Unsplash Source or relative placeholders with working ones.
 * @param {string} url - The image URL to fix
 * @returns {string} - A working image URL
 */
export function fixImageUrl(url) {
  if (!url || typeof url !== 'string') return HINH_ANH_MAC_DINH;

  const trimmedUrl = url.trim();
  
  if (trimmedUrl === "") return HINH_ANH_MAC_DINH;

  // Handle relative placeholder URLs like "1600x900/?vietnam,travel"
  if (trimmedUrl.startsWith('1600x900') || trimmedUrl.startsWith('800x600')) {
    const parts = trimmedUrl.split('?');
    const size = parts[0].replace('x', '/');
    const tags = parts[1] || 'travel,vietnam';
    // Use Lorem Flickr which is more reliable than Unsplash Source right now
    return `https://loremflickr.com/${size}/${tags}`;
  }

  // Handle deprecated source.unsplash.com URLs
  if (trimmedUrl.includes('source.unsplash.com')) {
    const parts = trimmedUrl.split('/');
    const lastPart = parts[parts.length - 1];
    
    if (lastPart.startsWith('?')) {
      // It's a keywords URL: source.unsplash.com/1600x900/?keywords
      const sizePart = parts[parts.length - 2] || '1600x900';
      const size = sizePart.replace('x', '/');
      const tags = lastPart.substring(1) || 'vietnam';
      return `https://loremflickr.com/${size}/${tags}`;
    } else if (lastPart.match(/^[a-zA-Z0-9_-]{10,12}$/)) {
      // It's a specific photo ID URL: source.unsplash.com/PHOTO_ID
      // Redirect to images.unsplash.com which is the direct CDN
      return `https://images.unsplash.com/photo-${lastPart}?auto=format&fit=crop&w=800&q=80`;
    }
  }

  return trimmedUrl;
}

export default {
  fixImageUrl,
  HINH_ANH_MAC_DINH
};
