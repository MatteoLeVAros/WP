import { NavLink } from "react-router-dom";
import { getMe } from "../api/userApi";
import { useEffect, useState } from "react";


export default function Sidebar() {
  const [me, setMe] = useState(null);

  useEffect(() => {
    const fetchMe = async () => {
      try {
        const userData = await getMe();
        setMe(userData);
      } catch (error) {
        console.error("Error fetching user data:", error);
        setMe(null);
      }
    };

    fetchMe();
  }, []);

  const getLinkClass = ({ isActive }) =>
    isActive ? "sidebar__link sidebar__link--active" : "sidebar__link";


  return (
    <aside className="sidebar">
      <div>
        <div className="sidebar__brand">WP671</div>
        <div className="sidebar__subtitle">Pilotage interne</div>
      </div>

      <nav className="sidebar__nav">
        <NavLink to="/" className={getLinkClass}>
          <span>Dashboard</span>
        </NavLink>
        
        <NavLink to="/demandes-intervention" className={getLinkClass}>
          <span>Demandes</span>
        </NavLink>

        {me?.roles?.includes("ROLE_ADMIN") && (
          <NavLink to="/campagnes" className={getLinkClass}>
            <span>Campagnes</span>
          </NavLink>
        )}

        <NavLink to="/taches" className={getLinkClass}>
          <span>Tâches</span>
        </NavLink>

        <NavLink to="/planning" className={getLinkClass}>
          <span>Planning</span>
        </NavLink>

        <NavLink to="/profile" className={getLinkClass}>
          <span>Profil</span>
        </NavLink>
      </nav>
    </aside>
  );
}
