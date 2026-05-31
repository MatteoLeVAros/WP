import { useEffect, useState } from "react";
import { Navigate } from "react-router-dom";
import { getMe } from "../api/userApi";

export default function RequireAdmin({ children }) {
  const [loading, setLoading] = useState(true);
  const [isAdmin, setIsAdmin] = useState(false);

  useEffect(() => {
    const checkAdmin = async () => {
      try {
        const me = await getMe();
        const roles = me.roles || [];
        setIsAdmin(roles.includes("ROLE_ADMIN"));
      } catch (error) {
        console.error("Erreur lors de la vérification admin :", error);
        setIsAdmin(false);
      } finally {
        setLoading(false);
      }
    };

    checkAdmin();
  }, []);

  if (loading) {
    return <div style={{ padding: 20 }}>Chargement...</div>;
  }

  if (!isAdmin) {
    return <Navigate to="/" replace />;
  }

  return children;
}