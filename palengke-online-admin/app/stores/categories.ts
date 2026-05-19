export const useCategoriesStore = defineStore("categories", () => {
  const categories = ref([
    {
      id: 1,
      name: "Vegetables",
      icon: "🥬",
      color: "#059669",
      products: 48,
      pct: 100,
    },
    {
      id: 2,
      name: "Fruits",
      icon: "🍎",
      color: "#dc2626",
      products: 36,
      pct: 75,
    },
    {
      id: 3,
      name: "Seafood",
      icon: "🐟",
      color: "#3b82f6",
      products: 28,
      pct: 58,
    },
    {
      id: 4,
      name: "Meat & Poultry",
      icon: "🍗",
      color: "#f59e0b",
      products: 32,
      pct: 67,
    },
    {
      id: 5,
      name: "Pork",
      icon: "🥩",
      color: "#ec4899",
      products: 20,
      pct: 42,
    },
    {
      id: 6,
      name: "Chicken",
      icon: "🐔",
      color: "#f97316",
      products: 18,
      pct: 38,
    },
    {
      id: 7,
      name: "Bundles",
      icon: "📦",
      color: "#8b5cf6",
      products: 12,
      pct: 25,
    },
    {
      id: 8,
      name: "Rice & Grains",
      icon: "🌾",
      color: "#14b8a6",
      products: 15,
      pct: 31,
    },
  ]);
  let nextId = 9;

  function addCategory(data) {
    categories.value.push({ id: nextId++, ...data, products: 0, pct: 0 });
  }

  function updateCategory(id, data) {
    const cat = categories.value.find((c) => c.id === id);
    if (cat) Object.assign(cat, data);
  }

  function deleteCategory(id) {
    categories.value = categories.value.filter((c) => c.id !== id);
  }

  return { categories, addCategory, updateCategory, deleteCategory };
});
