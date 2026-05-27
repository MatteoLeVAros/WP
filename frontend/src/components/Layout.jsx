import Sidebar from "./Sidebar";
import Navbar from "./Navbar";

export default function Layout({ children }) {
  return (
    <div className="app-shell">
      <Sidebar />

      <div className="main">
        <Navbar />
        <main className="page">{children}</main>
      </div>
    </div>
  );
}