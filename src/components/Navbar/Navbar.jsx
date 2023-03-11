import React from 'react';

import styles from './Navbar.module.css';
import logo from './Images/logo-icon.png';


const Navbar = () => {
  return (
    <div className={styles.navbar}>
        <div className="container">
          <div className="row">
            <div className="col-md-6">
                <div className='logo'>
                  <img src={logo} alt="Nature" />
                </div>
                <nav className='nav-links'>
                  <ul>
                    <li>
                    </li>
                  </ul>
                </nav>
            </div>
          </div>
        </div>
    </div>
  )
}

export default Navbar