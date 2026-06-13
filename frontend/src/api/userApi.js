import API from "./axios";

export const getMe = async () => {
  const res = await API.get("/me");
  return res.data;
};

export const getUsers = async () => {
  const res = await API.get("/users");
  return res.data;
};


export const getUser = async (id) => {
  const res = await API.get(`/users/${id}`);
  return res.data;
};
