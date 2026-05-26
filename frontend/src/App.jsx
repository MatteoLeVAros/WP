import { useState } from 'react'
import reactLogo from './assets/react.svg'
import viteLogo from './assets/vite.svg'
import heroImg from './assets/hero.png'
import './App.css'
import { AuthProvider } from './context/AuthContext'
import AppRouter from './router/AppRouter'

function App() {
  const [count, setCount] = useState(0)

  return (
    <AuthProvider>
      <AppRouter />
    </AuthProvider>
  )
}

export default App
