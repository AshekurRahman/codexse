import express from "express";
import asyncHandler from "express-async-handler";
import Product from "./../models/ProductModel.js";

const productRouter = express.Router();

productRouter.get(
  "/",
  asyncHandler(async (req, res) => {
    const products = await Product.find({});
    res.json(products);
  })
);

productRouter.get(
  "/:id",
  asyncHandler(async (req, res) => {
    const product = await Product.findById(req.params.id);
    if (product) {
      res.json(product);
    } else {
      res.status(400);
      throw new Error("Product not found.");
    }
  })
);

export default productRouter;
