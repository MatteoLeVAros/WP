import { NavLink } from "react-router-dom";


export default function Sidebar() {
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

        <NavLink to="/taches" className={getLinkClass}>
          <span>Tâches</span>
        </NavLink>

        <NavLink to="/campagnes" className={getLinkClass}>
          <span>Campagnes</span>
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
