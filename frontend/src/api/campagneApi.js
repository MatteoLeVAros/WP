import API from "./axios";

export const getCampagnes = async (filters = {}) => {
  const params = new URLSearchParams(filters).toString();
  const res = await API.get(`/campagnes?${params}`);
  return res.data;
};

export const createCampagne = async (data) => {
  const res = await API.post("/campagnes", data);
  return res.data;
};

export const deleteCampagne = async (id) => {
  await API.delete(`/campagnes/${id}`);
};

export const getCampagne = async (id) => {
  const res = await API.get(`/campagnes/${id}`);
  return res.data;
};