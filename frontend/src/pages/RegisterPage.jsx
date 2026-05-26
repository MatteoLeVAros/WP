import { useState } from "react";
import { useAuth } from "../context/AuthContext";

export default function RegisterPage() {
  const { register } = useAuth();

  const [form, setForm] = useState({
    email: "",
    password: "",
    nom: "",
    prenom: "",
  });

  const handleSubmit = async (e) => {
    e.preventDefault();

    try {
      await register(form);
      alert("Compte créé !");
      window.location.href = "/login";
    } catch (e) {
      alert("Erreur register");
    }
  };

  return (
    <div>
      <h1>Register</h1>

      <form onSubmit={handleSubmit}>
        <input placeholder="Email" onChange={(e) => setForm({ ...form, email: e.target.value })} />
        <input type="password" placeholder="Password" onChange={(e) => setForm({ ...form, password: e.target.value })} />
        <input placeholder="Nom" onChange={(e) => setForm({ ...form, nom: e.target.value })} />
        <input placeholder="Prenom" onChange={(e) => setForm({ ...form, prenom: e.target.value })} />

        <button type="submit">Register</button>
      </form>
    </div>
  );
}