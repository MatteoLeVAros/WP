import API from "./axios";

export const getTaches = async (filters = {}) => {
  const params = new URLSearchParams(filters).toString();
  const res = await API.get(`/taches?${params}`);
  return res.data;
};

export const createTache = async (data) => {
  const res = await API.post("/taches", data);
  return res.data;
};

export const deleteTache = async (id) => {
  await API.delete(`/taches/${id}`);
};


export const getTache = async (id) => {
  const res = await API.get(`/taches/${id}`);
  return res.data;
};

 