import API from "./axios";

export const getDemandesIntervention = async () => {
  const res = await API.get("/demandes-intervention");
  return res.data;
};

export const getDemandeIntervention = async (id) => {
  const res = await API.get(`/demandes-intervention/${id}`);
  return res.data;
};

export const createDemandeIntervention = async (data) => {
  const res = await API.post("/demandes-intervention", data);
  return res.data;
};

export const updateDemandeIntervention = async (id, data) => {
  const res = await API.put(`/demandes-intervention/${id}`, data);
  return res.data;
};

export const cancelDemandeIntervention = async (id) => {
  const res = await API.patch(`/demandes-intervention/${id}/cancel`);
  return res.data;
};
