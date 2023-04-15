import express from "express";
import asyncHandler from "express-async-handler";
import Products from "./data/Products.js";
import users from "./data/users.js";
import Product from "./models/ProductModel.js";
import User from "./models/UserModel.js";

const ImportData = express.Router();

ImportData.post(
  "/user",
  asyncHandler(async (req, res) => {
    await User.deleteMany({});
    const importUser = await User.insertMany(users);
    res.send({ importUser });
  })
);

ImportData.post(
  "/products",
  asyncHandler(async (req, res) => {
    await Product.deleteMany({});
    const importProduct = await Product.insertMany(Products);
    res.send({ importProduct });
  })
);

export default ImportData;
