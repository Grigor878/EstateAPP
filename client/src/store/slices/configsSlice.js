import { createSlice, createAsyncThunk } from "@reduxjs/toolkit";
import baseApi from "../../services/api/baseApi";
import { success } from "../../components/alerts/alerts";
import { getAxiosConfig } from "../../services/api/config";

const initialState = {
  loadingAddress: false,
  address: [],
  addedAddress: false,
  removedAddress: false,
  loadingSearch: false,
  searches: [],
  loadingExchange: false,
  exchange: [],
};

// Addresses
export const getConfigsAddresses = createAsyncThunk("configs", async () => {
  try {
    const { data } = await baseApi.get("/api/getAddress", getAxiosConfig());
    return data;
  } catch (err) {
    console.log(`Get Configs Adresses Error: ${err.message}`);
  }
});

export const addConfigsAddress = createAsyncThunk(
  "configs/addAddress",
  async ({ addedAddress }) => {
    try {
      await baseApi.post("/api/createAddress", addedAddress, getAxiosConfig());
    } catch (err) {
      console.log(`Add Address Error: ${err.message}`);
    }
  }
);

export const removeConfigsAddress = createAsyncThunk(
  "configs/removeAddress",
  async ({ removedAddress }) => {
    try {
      await baseApi.post(
        "/api/deleteAddress",
        removedAddress,
        getAxiosConfig()
      );
    } catch (err) {
      console.log(`Remove Address Error: ${err.message}`);
    }
  }
);

// Searches
export const getSearches = createAsyncThunk("configs/searches", async () => {
  try {
    const { data } = await baseApi.get(
      "/api/getRecentSearch",
      getAxiosConfig()
    );
    return data;
  } catch (err) {
    console.log(`Get Searches Error: ${err.message}`);
  }
});

// Exchanges
export const getExchangeData = createAsyncThunk(
  "configs/getExchange",
  async () => {
    try {
      const { data } = await baseApi.get("/api/getExchange");
      return data;
    } catch (err) {
      console.log(`Get Exchange Data Error: ${err.message}`);
    }
  }
);

export const setExchangeData = createAsyncThunk(
  "configs/setExchange",
  async ({ ex }) => {
    try {
      await baseApi.post("/api/setExchange", ex, getAxiosConfig());
    } catch (err) {
      console.log(`Set Exchange Data Error: ${err.message}`);
    }
  }
);

const structureSlice = createSlice({
  name: "configAddresses",
  initialState,
  reducers: {},
  extraReducers: (builder) => {
    builder
      // Address
      .addCase(getConfigsAddresses.pending, (state) => {
        state.loadingAddress = true;
      })
      .addCase(getConfigsAddresses.fulfilled, (state, action) => {
        state.loadingAddress = false;
        state.address = action.payload;
      })
      .addCase(addConfigsAddress.pending, (state) => {
        state.addedAddress = false;
      })
      .addCase(addConfigsAddress.fulfilled, (state) => {
        state.addedAddress = true;
      })
      .addCase(removeConfigsAddress.pending, (state) => {
        state.removedAddress = false;
      })
      .addCase(removeConfigsAddress.fulfilled, (state) => {
        state.removedAddress = true;
      })
      // Searches
      .addCase(getSearches.pending, (state) => {
        state.loadingSearch = true;
      })
      .addCase(getSearches.fulfilled, (state, action) => {
        state.loadingSearch = false;
        state.searches = action.payload;
      })
      // Exchnage
      .addCase(getExchangeData.pending, (state) => {
        state.loadingExchange = true;
      })
      .addCase(getExchangeData.fulfilled, (state, action) => {
        state.loadingExchange = false;
        state.exchange = action.payload;
      })
      .addCase(setExchangeData.fulfilled, () => {
        success(`Դոլարի կուրսը փոփոխված է։`);
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      });
  },
});

export default structureSlice.reducer;
