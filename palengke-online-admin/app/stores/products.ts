export const useProductsStore = defineStore("products", () => {
  const products = ref([
    {
      id: 1,
      name: "Fresh Tomatoes",
      category: "vegetables",
      seller: "Juan's Farm",
      sellerId: 1,
      price: 60,
      unit: "kg",
      stock: 120,
      status: "in_stock",
      image:
        "https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=200&h=200&fit=crop",
    },
    {
      id: 2,
      name: "Organic Eggplant",
      category: "vegetables",
      seller: "Juan's Farm",
      sellerId: 1,
      price: 45,
      unit: "kg",
      stock: 85,
      status: "in_stock",
      image:
        "https://images.unsplash.com/photo-1615484477778-ca3b77940c25?w=200&h=200&fit=crop",
    },
    {
      id: 3,
      name: "Ripe Mangoes",
      category: "fruits",
      seller: "Maria's Produce",
      sellerId: 2,
      price: 120,
      unit: "kg",
      stock: 50,
      status: "in_stock",
      image:
        "https://images.unsplash.com/photo-1553279768-865429fa0078?w=200&h=200&fit=crop",
    },
    {
      id: 4,
      name: "Fresh Bangus",
      category: "seafood",
      seller: "Fresh Fish Co.",
      sellerId: 3,
      price: 180,
      unit: "kg",
      stock: 30,
      status: "in_stock",
      image:
        "https://images.unsplash.com/photo-1579165466741-7f35e475518f?w=200&h=200&fit=crop",
    },
    {
      id: 5,
      name: "Pork Belly",
      category: "meat",
      seller: "Meat Shop Inc.",
      sellerId: 4,
      price: 280,
      unit: "kg",
      stock: 0,
      status: "out_of_stock",
      image:
        "https://images.unsplash.com/photo-1559742811-822f4580b12e?w=200&h=200&fit=crop",
    },
    {
      id: 6,
      name: "Pechay Baguio",
      category: "vegetables",
      seller: "Juan's Farm",
      sellerId: 1,
      price: 35,
      unit: "bundle",
      stock: 200,
      status: "in_stock",
      image:
        "https://images.unsplash.com/photo-1615485290474-2e51f8b92c7c?w=200&h=200&fit=crop",
    },
    {
      id: 7,
      name: "Green Papaya",
      category: "vegetables",
      seller: "Green Valley Farm",
      sellerId: 5,
      price: 40,
      unit: "pc",
      stock: 65,
      status: "in_stock",
      image:
        "https://images.unsplash.com/photo-1526318472351-c75fcf070305?w=200&h=200&fit=crop",
    },
    {
      id: 8,
      name: "Chicken Leg Quarters",
      category: "meat",
      seller: "Meat Shop Inc.",
      sellerId: 4,
      price: 190,
      unit: "kg",
      stock: 45,
      status: "in_stock",
      image:
        "https://images.unsplash.com/photo-1587593810167-a84920ea0781?w=200&h=200&fit=crop",
    },
    {
      id: 9,
      name: "Imported Grapes",
      category: "fruits",
      seller: "Maria's Produce",
      sellerId: 2,
      price: 250,
      unit: "kg",
      stock: 20,
      status: "in_stock",
      image:
        "https://images.unsplash.com/photo-1537640538966-79f369143f8f?w=200&h=200&fit=crop",
    },
    {
      id: 10,
      name: "Fresh Tilapia",
      category: "seafood",
      seller: "Fresh Fish Co.",
      sellerId: 3,
      price: 140,
      unit: "kg",
      stock: 55,
      status: "in_stock",
      image:
        "https://images.unsplash.com/photo-1579187707643-35646d22b596?w=200&h=200&fit=crop",
    },
    {
      id: 11,
      name: "Vegetable Bundle",
      category: "bundles",
      seller: "Green Valley Farm",
      sellerId: 5,
      price: 350,
      unit: "set",
      stock: 25,
      status: "in_stock",
      image:
        "https://images.unsplash.com/photo-1540420773420-3366772f4999?w=200&h=200&fit=crop",
    },
    {
      id: 12,
      name: "Beef Lean Meat",
      category: "meat",
      seller: "Meat Shop Inc.",
      sellerId: 4,
      price: 380,
      unit: "kg",
      stock: 0,
      status: "out_of_stock",
      image:
        "https://images.unsplash.com/photo-1557462053-9f6b96bae7a1?w=200&h=200&fit=crop",
    },
    {
      id: 13,
      name: "Coconut",
      category: "fruits",
      seller: "Sweet Harvest",
      sellerId: 6,
      price: 25,
      unit: "pc",
      stock: 100,
      status: "in_stock",
      image:
        "https://images.unsplash.com/photo-1544022613-e87ca75a784a?w=200&h=200&fit=crop",
    },
    {
      id: 14,
      name: "Shrimp (Sugpo)",
      category: "seafood",
      seller: "Coastal Catch",
      sellerId: 7,
      price: 320,
      unit: "kg",
      stock: 18,
      status: "in_stock",
      image:
        "https://images.unsplash.com/photo-1565680018434-b1d8e0e9b1c5?w=200&h=200&fit=crop",
    },
    {
      id: 15,
      name: "Organic Brown Rice",
      category: "bundles",
      seller: "Organic Choice",
      sellerId: 8,
      price: 65,
      unit: "kg",
      stock: 0,
      status: "out_of_stock",
      image:
        "https://images.unsplash.com/photo-1586201375761-83865001e31c?w=200&h=200&fit=crop",
    },
  ]);

  let nextId = 16;

  function addProduct(data) {
    products.value.unshift({
      id: nextId++,
      name: data.name,
      category: data.category,
      seller: data.seller,
      sellerId: data.sellerId,
      price: Number(data.price),
      unit: data.unit,
      stock: Number(data.stock),
      status: Number(data.stock) > 0 ? "in_stock" : "out_of_stock",
      image: data.image || "",
    });
  }

  function updateProduct(id, data) {
    const p = products.value.find((p) => p.id === id);
    if (p) {
      p.name = data.name;
      p.category = data.category;
      p.seller = data.seller;
      p.sellerId = data.sellerId;
      p.price = Number(data.price);
      p.unit = data.unit;
      p.stock = Number(data.stock);
      p.status = Number(data.stock) > 0 ? "in_stock" : "out_of_stock";
      if (data.image !== undefined) p.image = data.image;
    }
  }

  function deleteProduct(id) {
    products.value = products.value.filter((p) => p.id !== id);
  }

  const pendingReviews = ref([
    {
      id: "PR-001",
      name: "Fresh Ampalaya",
      category: "vegetables",
      seller: "Juan's Farm",
      sellerId: 1,
      price: 50,
      unit: "kg",
      stock: 80,
      description: "Freshly harvested bitter gourd from our organic farm.",
      submittedAt: "2026-05-15",
      image:
        "https://images.unsplash.com/photo-1590604682829-66e4d83b0e10?w=200&h=200&fit=crop",
    },
    {
      id: "PR-002",
      name: "Dried Fish (Daing)",
      category: "seafood",
      seller: "Fresh Fish Co.",
      sellerId: 3,
      price: 200,
      unit: "pack",
      stock: 40,
      description: "Sun-dried bangus, ready to fry. 500g per pack.",
      submittedAt: "2026-05-14",
      image:
        "https://images.unsplash.com/photo-1598645217397-89c342c1b33b?w=200&h=200&fit=crop",
    },
    {
      id: "PR-003",
      name: "Sweet Corn (Native)",
      category: "vegetables",
      seller: "Maria's Produce",
      sellerId: 2,
      price: 35,
      unit: "pc",
      stock: 150,
      description: "Native sweet corn from Bukidnon. Sweet and juicy.",
      submittedAt: "2026-05-14",
      image:
        "https://images.unsplash.com/photo-1551754655-cd27e38d2076?w=200&h=200&fit=crop",
    },
    {
      id: "PR-004",
      name: "Frozen Liempo",
      category: "meat",
      seller: "Meat Shop Inc.",
      sellerId: 4,
      price: 250,
      unit: "kg",
      stock: 60,
      description: "Premium pork belly, vacuum-sealed and frozen.",
      submittedAt: "2026-05-13",
      image:
        "https://images.unsplash.com/photo-1559742811-822f4580b12e?w=200&h=200&fit=crop",
    },
    {
      id: "PR-005",
      name: "Banana (Saba)",
      category: "fruits",
      seller: "Sweet Harvest",
      sellerId: 6,
      price: 15,
      unit: "pc",
      stock: 300,
      description: "Fresh saba bananas, perfect for turon or boiling.",
      submittedAt: "2026-05-12",
      image:
        "https://images.unsplash.com/photo-1571771894821-ce9b6ba11d94?w=200&h=200&fit=crop",
    },
  ]);

  function approveReview(id) {
    const r = pendingReviews.value.find((p) => p.id === id);
    if (r) {
      products.value.unshift({
        id: nextId++,
        name: r.name,
        category: r.category,
        seller: r.seller,
        sellerId: r.sellerId,
        price: r.price,
        unit: r.unit,
        stock: r.stock,
        status: r.stock > 0 ? "in_stock" : "out_of_stock",
        image: r.image || "",
      });
      pendingReviews.value = pendingReviews.value.filter((p) => p.id !== id);
    }
  }

  function rejectReview(id) {
    pendingReviews.value = pendingReviews.value.filter((p) => p.id !== id);
  }

  return {
    products,
    addProduct,
    updateProduct,
    deleteProduct,
    pendingReviews,
    approveReview,
    rejectReview,
  };
});
