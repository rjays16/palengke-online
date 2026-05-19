export const useThemeStore = defineStore("theme", () => {
  const isDark = ref(false);

  function init() {
    const saved =
      typeof localStorage !== "undefined"
        ? localStorage.getItem("palengke_theme")
        : null;
    if (saved === "dark") {
      isDark.value = true;
      document.documentElement.classList.add("dark-mode");
    }
  }

  function toggle() {
    isDark.value = !isDark.value;
    if (isDark.value) {
      document.documentElement.classList.add("dark-mode");
      localStorage.setItem("palengke_theme", "dark");
    } else {
      document.documentElement.classList.remove("dark-mode");
      localStorage.setItem("palengke_theme", "light");
    }
  }

  return { isDark, init, toggle };
});
