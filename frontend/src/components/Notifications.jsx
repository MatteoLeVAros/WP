import { useEffect, useState } from "react";
import { getNotifications, markAsRead } from "../api/notificationApi";

export default function Notifications() {
  const [notifications, setNotifications] = useState([]);
  const [open, setOpen] = useState(false);

  const fetchNotifications = async () => {
    const data = await getNotifications();
    setNotifications(data);
  };

  useEffect(() => {
    fetchNotifications();
  }, []);

  const handleRead = async (id) => {
    await markAsRead(id);
    fetchNotifications();
  };

  const unreadCount = notifications.filter((n) => !n.estLue).length;

  return (
    <div style={{ position: "relative" }}>
      <button onClick={() => setOpen(!open)}>
        🔔 ({unreadCount})
      </button>

      {open && (
        <div
          style={{
            position: "absolute",
            top: 40,
            right: 0,
            width: 300,
            background: "white",
            border: "1px solid #ccc",
            padding: 10,
            zIndex: 100
          }}
        >
          <h4>Notifications</h4>

          {notifications.length === 0 && <p>Aucune notification</p>}

          {notifications.map((n) => (
            <div
              key={n.id}
              style={{
                padding: 5,
                background: n.estLue ? "#eee" : "#ddd",
                marginBottom: 5
              }}
            >
              <strong>{n.titre}</strong>
              <p>{n.message}</p>

              {!n.estLue && (
                <button onClick={() => handleRead(n.id)}>
                  Marquer comme lue
                </button>
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  );
}