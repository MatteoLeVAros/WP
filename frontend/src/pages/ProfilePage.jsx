import { useAuth } from "../context/AuthContext";

export default function ProfilePage() {
  const { user, logout } = useAuth();

  if (!user) return null;

  return (
    <div>
      <h1>Profile</h1>

      <p>Email: {user.email}</p>
      <p>Nom: {user.nom}</p>
      <p>Prenom: {user.prenom}</p>

      <button onClick={logout}>Logout</button>
    </div>
  );
}