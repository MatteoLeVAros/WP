import { useAuth } from "../context/AuthContext";
import Notifications from "./Notifications";

export default function Navbar() {
  const { user, logout } = useAuth();


  return (
    <header className="navbar">
      <div className="navbar__title">Tableau de bord de validation</div>

      <div className="navbar__right">
        <Notifications />

        {user && (
          <>
            <span className="navbar__user">{user.email}</span>
            <button className="btn btn--secondary" onClick={logout}>
              Logout
            </button>
          </>
        )}
      </div>
    </header>
  );
}
