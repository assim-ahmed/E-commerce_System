import { STORAGE_URL } from './constants'

// الحصول على رابط الصورة الكامل
export const getImageUrl = (imagePath) => {
  // إذا كان المسار array خذ أول عنصر
  const path = Array.isArray(imagePath) ? imagePath[0] : imagePath
  
  // إذا كان المسار موجود و ليس فارغ
  if (path && typeof path === 'string' && path.trim() !== '') {
    return `${STORAGE_URL}/${path}`
  }
  
  // صورة وهمية حسب اسم المنتج (لضمان تنوع الألوان)
  return getPlaceholderImage()
}

// الحصول على صورة وهمية بناءً على نص (لتنوع الألوان)
export const getPlaceholderImage = (seed = 'default') => {
  // ألوان متنوعة للصور الوهمية
  const colors = [
    'f59e0b', // برتقالي
    '10b981', // أخضر
    '3b82f6', // أزرق
    'ef4444', // أحمر
    '8b5cf6', // بنفسجي
    'ec4899', // وردي
    '06b6d4', // فيروزي
    'f97316', // برتقالي غامق
  ]
  
  // اختيار لون بناءً على النص المدخل
  const hash = seed.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0)
  const color = colors[hash % colors.length]
  
  return `https://placehold.co/400x400/${color}/ffffff?text=${encodeURIComponent('🛍️')}`
}

// صورة وهمية للقسم الرئيسي
export const getHeroImage = () => {
  return 'https://placehold.co/1920x600/f59e0b/ffffff?text=بوينت+للتسوق'
}

// صورة وهمية للمنتج المميز
export const getFeaturedImage = () => {
  return 'https://placehold.co/800x800/8b5cf6/ffffff?text=✨'
}