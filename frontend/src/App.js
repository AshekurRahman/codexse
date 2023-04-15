import { Route, BrowserRouter as Router, Routes } from "react-router-dom";
import "react-toastify/dist/ReactToastify.css";
import "./assets/css/global.css";
import Layout from "./components/Layout";
import Forgot from "./components/Pages/Forgot/Forgot";
import Home from "./components/Pages/Home/Home";
import Login from "./components/Pages/Login/Login";
import News from "./components/Pages/News/News";
import CreatePost from "./components/Pages/Posts/CreatePost";
import Privacy from "./components/Pages/Privacy/Privacy";
import Products from "./components/Pages/Products/Products";
import SingleProduct from "./components/Pages/Products/SingleProduct";
import Register from "./components/Pages/Register/Register";

function App() {
  return (
    <div className="app-wrapper">
      <Router>
        <Layout>
          <Routes>
            <Route exact path="/" element={<Home />} />
            <Route path="/news" element={<News />} />
            <Route path="/products" element={<Products />} />
            <Route path="/products/:id" element={<SingleProduct />} />
            <Route path="/login" element={<Login />} />
            <Route path="/register" element={<Register />} />
            <Route path="/forgot" element={<Forgot />} />
            <Route path="/privacy-policy" element={<Privacy />} />
            <Route path="/post-create" element={<CreatePost />} />
          </Routes>
        </Layout>
      </Router>
    </div>
  );
}

export default App;