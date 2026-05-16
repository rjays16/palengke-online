export const useBannersStore = defineStore('banners', () => {
  const banners = ref([
    { id: 1, title: 'Fresh Seafood Sale', subtitle: 'Up to 30% off on all seafood', image: '/images/banner-seafood.jpg', link: '/category/seafood', active: true, sortOrder: 1 },
    { id: 2, title: 'Farm Fresh Vegetables', subtitle: 'Direct from local farms', image: '/images/banner-veggies.jpg', link: '/category/vegetables', active: true, sortOrder: 2 },
    { id: 3, title: 'New Merchants Week', subtitle: 'Discover new sellers in your area', image: '/images/banner-merchants.jpg', link: '/sellers', active: false, sortOrder: 3 },
    { id: 4, title: 'Free Delivery', subtitle: 'On orders over ₱500', image: '/images/banner-delivery.jpg', link: '/info/delivery', active: true, sortOrder: 4 },
  ])
  let nextId = 5

  function addBanner(data) {
    banners.value.push({ id: nextId++, ...data })
  }
  function updateBanner(id, data) {
    const b = banners.value.find(b => b.id === id)
    if (b) Object.assign(b, data)
  }
  function deleteBanner(id) {
    banners.value = banners.value.filter(b => b.id !== id)
  }
  function toggleActive(id) {
    const b = banners.value.find(b => b.id === id)
    if (b) b.active = !b.active
  }

  return { banners, addBanner, updateBanner, deleteBanner, toggleActive }
})
