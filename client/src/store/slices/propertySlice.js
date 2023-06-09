import { createSlice, createAsyncThunk } from "@reduxjs/toolkit";
import baseApi from "../../apis/baseApi";
import { getAxiosConfig } from "../../apis/config";
import { success } from "../../components/swal/swal";
import { APP_BASE_URL } from "../../apis/config";

const initialState = {
  structureLoading: false,
  structure: null,
  propertyLoading: false,
  propertyData: null,
  postAddLoading: false,
  uploadPhoto: {},
  uploadFile: {},
  yandex: [],
  keyword: [],
  postEditLoading: false,
};

// get property structure
export const getPropertyStructure = createAsyncThunk("property", async () => {
  try {
    const { data } = await baseApi.get("/api/getAllStructure");
    return data;
  } catch (err) {
    console.log(`Get Property Structure Error: ${err.message}`);
  }
});

// get property data
export const getPropertyData = createAsyncThunk(
  "property/getPropertyData",
  async () => {
    try {
      const { data } = await baseApi.get("/api/getHome");
      return data;
    } catch (err) {
      console.log(`Get Property Data Error: ${err.message}`);
    }
  }
);

// post added data
export const addPropertyData = createAsyncThunk(
  "property/addPropertyData",
  async ({ addProperty }, thunkAPI) => {
    try {
      const response = await baseApi.post(
        "/api/addHome",
        addProperty,
        getAxiosConfig()
      );
      thunkAPI.dispatch(addPropertyImgs(response.data));
      return response.data;
    } catch (err) {
      console.log(`Add Property Data Sending Error: ${err.message}`);
      throw err;
    }
  }
);

// post added imgs
export const addPropertyImgs = createAsyncThunk(
  "property/addPropertyImgs",
  async (id, thunkAPI) => {
    try {
      const state = thunkAPI.getState();
      let uploadPhoto = state.property.uploadPhoto;
      await baseApi.post(`/api/multyPhoto/${id}`, uploadPhoto);
      thunkAPI.dispatch(addPropertyFiles(id));
    } catch (err) {
      console.log(`Add Property Imgs Sending Error: ${err.message}`);
    }
  }
);

// post added files
export const addPropertyFiles = createAsyncThunk(
  "property/addPropertyFiles",
  async (id, thunkAPI) => {
    try {
      const state = thunkAPI.getState();
      let uploadFile = state.property.uploadFile;
      await baseApi.post(`/api/documentUpload/${id}`, uploadFile);
      thunkAPI.dispatch(addPropertyYandex(id));
    } catch (err) {
      console.log(`Add Property Files Sending Error: ${err.message}`);
    }
  }
);

// post added yandex
export const addPropertyYandex = createAsyncThunk(
  "property/addPropertyYandex",
  async (id, thunkAPI) => {
    try {
      const state = thunkAPI.getState();
      let yandex = state.property.yandex;
      await baseApi.post(`/api/addYandexLocation/${id}`, yandex);
      thunkAPI.dispatch(addPropertyKeyword(id));
    } catch (err) {
      console.log(`Add Property Yandex Data Sending Error: ${err.message}`);
    }
  }
);

// post added keyword
export const addPropertyKeyword = createAsyncThunk(
  "property/addPropertyKeyword",
  async (id, thunkAPI) => {
    try {
      const state = thunkAPI.getState();
      let keyword = state.property.keyword;
      const response = await baseApi.post(`/api/addKeyword/${id}`, keyword);
      return response.status;
    } catch (err) {
      console.log(`Add Property Keyword Sending Error: ${err.message}`);
    }
  }
);

// post edited data
export const editPropertyData = createAsyncThunk(
  "property/editPropertyData",
  async ({ editProperty, propertyId }, { rejectWithValue, dispatch }) => {
    try {
      const response = await baseApi.post(
        `/api/editHome/${propertyId}`,
        editProperty,
        getAxiosConfig()
      );
      dispatch(editPropertyImgs(response.data));
      return response.data;
    } catch (err) {
      console.log(`Edit Property Data Sending Error: ${err.message}`);
      throw rejectWithValue(err.message);
    }
  }
);

// post edited imgs
export const editPropertyImgs = createAsyncThunk(
  "property/editPropertyImgs",
  async (propertyId, thunkAPI) => {
    try {
      const state = thunkAPI.getState();
      let uploadPhoto = state.property.uploadPhoto;
      await baseApi.post(`/api/editMultyPhoto/${propertyId}`, uploadPhoto);
      thunkAPI.dispatch(editPropertyFiles(propertyId));
    } catch (err) {
      console.log(`Edit Property Imgs Sending Error: ${err.message}`);
    }
  }
);

// post edited files
export const editPropertyFiles = createAsyncThunk(
  "property/editPropertyFiles",
  async (propertyId, thunkAPI) => {
    try {
      const state = thunkAPI.getState();
      let uploadFile = state.property.uploadFile;
      await baseApi.post(`/api/editDocumentUpload/${propertyId}`, uploadFile);
      thunkAPI.dispatch(editPropertyYandex(propertyId));
    } catch (err) {
      console.log(`Edit Property Files Sending Error: ${err.message}`);
    }
  }
);

// post edited yandex
export const editPropertyYandex = createAsyncThunk(
  "property/editPropertyYandex",
  async (propertyId, thunkAPI) => {
    try {
      const state = thunkAPI.getState();
      let yandex = state.property.yandex;
      await baseApi.post(`/api/editYandexLocation/${propertyId}`, yandex);
      thunkAPI.dispatch(editPropertyKeyword(propertyId));
    } catch (err) {
      console.log(`Edit Property Yandex Data Sending Error: ${err.message}`);
    }
  }
);

// post edited keyword
export const editPropertyKeyword = createAsyncThunk(
  "property/editPropertyKeyword",
  async (propertyId, thunkAPI) => {
    try {
      const state = thunkAPI.getState();
      let keyword = state.property.keyword;
      await baseApi.post(`/api/editKeyword/${propertyId}`, keyword);
    } catch (err) {
      console.log(`Edit Property Keyword Sending Error: ${err.message}`);
    }
  }
);

const structureSlice = createSlice({
  name: "property",
  initialState,
  reducers: {
    setUploadPhoto: (state, action) => {
      state.uploadPhoto = action.payload;
    },
    setUploadFile: (state, action) => {
      state.uploadFile = action.payload;
    },
    setYandex: (state, action) => {
      state.yandex = action.payload;
    },
    setKeyword: (state, action) => {
      state.keyword = action.payload;
    },
  },
  extraReducers: (builder) => {
    builder
      .addCase(getPropertyStructure.pending, (state) => {
        state.structureLoading = true;
      })
      .addCase(getPropertyStructure.fulfilled, (state, action) => {
        state.structureLoading = false;
        state.structure = action.payload;
      })
      .addCase(getPropertyData.pending, (state) => {
        state.propertyLoading = true;
      })
      .addCase(getPropertyData.fulfilled, (state, action) => {
        state.propertyLoading = false;
        state.propertyData = action.payload;
      })
      // add property
      .addCase(addPropertyData.pending, (state) => {
        state.postAddLoading = true;
      })
      .addCase(addPropertyKeyword.fulfilled, (state) => {
        state.postAddLoading = false;
        success("Property added!");
        window.location = `${APP_BASE_URL}/dashboard/properties`;
        // window.location.replace(`${APP_BASE_URL}/dashboard/properties`);
      })
      // edit property
      .addCase(editPropertyData.pending, (state) => {
        state.postEditLoading = true;
      })
      .addCase(editPropertyData.fulfilled, (state) => {
        state.postEditLoading = false;
      });
  },
});

export const { setUploadPhoto, setUploadFile, setYandex, setKeyword } =
  structureSlice.actions;
export default structureSlice.reducer;
