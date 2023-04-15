import axios from "axios";
import {
  CART_ADD_ITEM_FAIL,
  CART_ADD_ITEM_REQUEST,
  CART_ADD_ITEM_SUCCESS,
  CART_REMOVE_ITEM_FAIL,
  CART_REMOVE_ITEM_REQUEST,
  CART_REMOVE_ITEM_SUCCESS,
} from "../Constants/CartConstants";

export const addToCart = (userId, productId, quantity) => async (dispatch) => {
  try {
    dispatch({ type: CART_ADD_ITEM_REQUEST });

    const config = {
      headers: {
        "Content-Type": "application/json",
      },
    };

    const { data } = await axios.post(
      "/api/cart/add",
      { userId, productId, quantity },
      config
    );

    dispatch({
      type: CART_ADD_ITEM_SUCCESS,
      payload: data,
    });
  } catch (error) {
    dispatch({
      type: CART_ADD_ITEM_FAIL,
      payload:
        error.response && error.response.data.message
          ? error.response.data.message
          : error.message,
    });
  }
};

export const removeFromCart = (userId, productId) => async (dispatch) => {
  try {
    dispatch({ type: CART_REMOVE_ITEM_REQUEST });

    const { data } = await axios.delete(
      `/api/cart/remove/${userId}/${productId}`
    );

    dispatch({
      type: CART_REMOVE_ITEM_SUCCESS,
      payload: data,
    });
  } catch (error) {
    dispatch({
      type: CART_REMOVE_ITEM_FAIL,
      payload:
        error.response && error.response.data.message
          ? error.response.data.message
          : error.message,
    });
  }
};
