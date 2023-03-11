import React from 'react'
import './App.css'; // Import css stylesheet as styles
import Navbar from './components/Navbar/Navbar';
import Products from './components/Products/Products';

const App = () => {
  return (
    <div>
        <Navbar />
        <Products />
    </div>
  )
}

export default App