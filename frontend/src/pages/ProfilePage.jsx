import { useEffect, useState } from "react";
import { useAuth } from "../context/AuthContext";
import { getUsers, getUser } from "../api/userApi";

export default function ProfilePage() {
  const { user, logout } = useAuth();

  const [users, setUsers] = useState([]);
  const [selectedUser, setSelectedUser] = useState(null);
  const [loadingUsers, setLoadingUsers] = useState(false);
  const [loadingSelectedUser, setLoadingSelectedUser] = useState(false);
  const [error, setError] = useState("");

  const isAdmin = user?.roles?.includes("ROLE_ADMIN");

  useEffect(() => {
    if (!isAdmin) return;

    const fetchUsers = async () => {
      try {
        setLoadingUsers(true);
        setError("");

        const data = await getUsers();
        setUsers(data);
      } catch (e) {
        console.error("Erreur chargement utilisateurs :", e);
        setError("Impossible de charger la liste des utilisateurs.");
      } finally {
        setLoadingUsers(false);
      }
    };

    fetchUsers();
  }, [isAdmin]);

  const handleSelectUser = async (id) => {
    try {
      setLoadingSelectedUser(true);
      setError("");

      const data = await getUser(id);
      setSelectedUser(data);
    } catch (e) {
      console.error("Erreur chargement utilisateur :", e);
      setError("Impossible de charger le profil sélectionné.");
    } finally {
      setLoadingSelectedUser(false);
    }
  };

  const renderDisponibilite = (disponibilite) => {
    return disponibilite ? (
      <span className="badge badge--green">Disponible</span>
    ) : (
      <span className="badge badge--red">Non disponible</span>
    );
  };

  const renderRoles = (roles) => {
    if (!roles?.length) return "Aucun rôle";

    return roles.map((role) => (
      <span key={role} className="badge badge--blue" style={{ marginRight: 6 }}>
        {role}
      </span>
    ));
  };

  if (!user) return null;

  return (
    <div className="page">
      <div className="page__header">
        <div>
          <h1 className="page__title">Profil</h1>
          <p className="page__subtitle">
            Consulte les informations de ton profil.
          </p>
        </div>
      </div>

      {error && <div className="alert alert--danger">{error}</div>}

      <section className="card">
        <h2 className="card__title">Mon profil</h2>

        <div className="profile-field">
          <span className="profile-field__label">Email</span>
          <span className="profile-field__value">
            {user.email || "Non renseigné"}
          </span>
        </div>

        <div className="profile-field">
          <span className="profile-field__label">Nom</span>
          <span className="profile-field__value">
            {user.nom || "Non renseigné"}
          </span>
        </div>

        <div className="profile-field">
          <span className="profile-field__label">Prénom</span>
          <span className="profile-field__value">
            {user.prenom || "Non renseigné"}
          </span>
        </div>

        <div className="profile-field">
          <span className="profile-field__label">Téléphone</span>
          <span className="profile-field__value">
            {user.telephone || "Non renseigné"}
          </span>
        </div>

        <div className="profile-field">
          <span className="profile-field__label">Fonction</span>
          <span className="profile-field__value">
            {user.fonction || "Non renseignée"}
          </span>
        </div>

        <div className="profile-field">
          <span className="profile-field__label">Disponibilité</span>
          <span className="profile-field__value">
            {renderDisponibilite(user.disponibilite)}
          </span>
        </div>

        <div className="profile-field">
          <span className="profile-field__label">Rôles</span>
          <span className="profile-field__value">
            {renderRoles(user.roles)}
          </span>
        </div>

        <div className="profile-actions">
          <button className="btn btn--danger" onClick={logout}>
            Se déconnecter
          </button>
        </div>
      </section>

      {isAdmin && (
        <section className="section">
          <div className="page__header">
            <div>
              <h2 className="page__title">Profils utilisateurs</h2>
              <p className="page__subtitle">
                Sélectionne un utilisateur pour afficher les informations
                détaillées du profil.
              </p>
            </div>
          </div>

          {loadingUsers ? (
            <div className="card">
              <p className="empty-state">Chargement des utilisateurs...</p>
            </div>
          ) : (
            <div className="profile-layout">
              <div className="card">
                <h3 className="card__title">Liste des profils</h3>

                {users.length > 0 ? (
                  <ul className="profile-list">
                    {users.map((u) => (
                      <li key={u.id} className="profile-list__item">
                        <button
                          onClick={() => handleSelectUser(u.id)}
                          className={
                            selectedUser?.id === u.id
                              ? "profile-list__button profile-list__button--active"
                              : "profile-list__button"
                          }
                        >
                          <span className="profile-list__name">
                            {u.prenom} {u.nom}
                          </span>
                          <span className="profile-list__email">
                            {u.email}
                          </span>
                        </button>
                      </li>
                    ))}
                  </ul>
                ) : (
                  <p className="empty-state">Aucun utilisateur trouvé.</p>
                )}
              </div>

              <div className="card profile-detail">
                <h3 className="card__title">Détail du profil</h3>

                {loadingSelectedUser ? (
                  <p className="empty-state">Chargement du profil...</p>
                ) : selectedUser ? (
                  <>
                    <div className="profile-field">
                      <span className="profile-field__label">ID</span>
                      <span className="profile-field__value">
                        {selectedUser.id}
                      </span>
                    </div>

                    <div className="profile-field">
                      <span className="profile-field__label">Email</span>
                      <span className="profile-field__value">
                        {selectedUser.email || "Non renseigné"}
                      </span>
                    </div>

                    <div className="profile-field">
                      <span className="profile-field__label">Nom</span>
                      <span className="profile-field__value">
                        {selectedUser.nom || "Non renseigné"}
                      </span>
                    </div>

                    <div className="profile-field">
                      <span className="profile-field__label">Prénom</span>
                      <span className="profile-field__value">
                        {selectedUser.prenom || "Non renseigné"}
                      </span>
                    </div>

                    <div className="profile-field">
                      <span className="profile-field__label">Téléphone</span>
                      <span className="profile-field__value">
                        {selectedUser.telephone || "Non renseigné"}
                      </span>
                    </div>

                    <div className="profile-field">
                      <span className="profile-field__label">Fonction</span>
                      <span className="profile-field__value">
                        {selectedUser.fonction || "Non renseignée"}
                      </span>
                    </div>

                    <div className="profile-field">
                      <span className="profile-field__label">
                        Disponibilité
                      </span>
                      <span className="profile-field__value">
                        {renderDisponibilite(selectedUser.disponibilite)}
                      </span>
                    </div>

                    <div className="profile-field">
                      <span className="profile-field__label">Rôles</span>
                      <span className="profile-field__value">
                        {renderRoles(selectedUser.roles)}
                      </span>
                    </div>
                  </>
                ) : (
                  <p className="empty-state">
                    Sélectionne un profil dans la liste.
                  </p>
                )}
              </div>
            </div>
          )}
        </section>
      )}
    </div>
  );
}