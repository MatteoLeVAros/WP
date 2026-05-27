import API from "./axios";

export const getCommentairesByCampagne = async (campagneId) => {
  const res = await API.get(`/commentaires?campagne=${campagneId}`);
  return res.data;
};

export const createCommentaire = async (data) => {
  const res = await API.post("/commentaires", data);
  return res.data;
};

export const deleteCommentaire = async (id) => {
  await API.delete(`/commentaires/${id}`);
};
