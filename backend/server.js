import dotenv from "dotenv";
import express from "express";
import ImportData from "./DataImport.js";
import { errorHandler, notFound } from "./Middleware/Error.js";
import productRouter from "./Routes/ProductRoutes.js";
import UserRouter from "./Routes/UserRoutes.js";
import connectDatabase from "./config/MongoDb.js";

const app = express();
dotenv.config();
connectDatabase();
app.use(express.json());

//API
app.use("/api/import", ImportData);
app.use("/api/products", productRouter);
app.use("/api/users", UserRouter);

//Error Handler
app.use(notFound);
app.use(errorHandler);

const PORT = process.env.PORT || 1000;

app.listen(PORT, () => {
  console.log(`Server running in ${PORT}`);
});
