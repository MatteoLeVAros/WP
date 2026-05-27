import API from "./axios";

export const getPlanning = async (filters = {}) => {
  const cleanFilters = Object.fromEntries(
    Object.entries(filters).filter(([, value]) => value !== undefined && value !== null && value !== "")
  );

  const query = new URLSearchParams(cleanFilters).toString();
  const url = query ? `/planning?${query}` : "/planning";

  const res = await API.get(url);
  return res.data;
};