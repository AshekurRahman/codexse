import React, { useEffect } from 'react'
import styles from './Products.module.css'; // Import css modules stylesheet as styles




const Products = () => {

  const getProducts = async () => {
    try {      
      const response = await fetch('https://dummyjson.com/products');
      const actualData = await response.json();
      console.log(actualData);
    } catch (error) {
      console.log(error);
    }
  }

  useEffect( () => {
    getProducts();    
  }, []);


  return (
    <div className={styles.products} >products</div>
  )
}

export default Products