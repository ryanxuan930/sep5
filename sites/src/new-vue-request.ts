/* eslint-disable max-len */
/* eslint-disable no-param-reassign */
/*
This module is to provide an easy way to fetch data from the server.
*/

import axios from 'axios';

type TRef = {
  value: any;
  [key: string]: any;
}

interface IVueRequest {
  Get(url: string, refReturn: TRef, sameOrgin: boolean, haveAuth: boolean): Promise<any>;
  Post(url: string, dataset: any, refReturn: any, sameOrgin: boolean, haveAuth: boolean): Promise<any>;
  Patch(url: string, dataset: any, refReturn: any, sameOrgin: boolean, haveAuth: boolean, hasFile: boolean): Promise<any>;
  Delete(url: string, refReturn: TRef, sameOrgin: boolean, haveAuth: boolean): Promise<any>;
}

export default class VueRequest implements IVueRequest {
  private headerPrefix = '';
  private authToken = '';
  private acceptHeader = 'application/json';
  private contentType = 'application/json; charset=utf-8';
  constructor(token: string|undefined = undefined) {
    if (token !== undefined) {
      this.authToken = `Bearer ${token}`;
    }
  }

  // error handler
  private ErrHdl(error: any): void {
    console.error(error);
    if (error.response) {
      if (error.response.data.message === 'Unauthenticated.') {
        alert('請重新登入');
      } else if (error.response.data.message === 'Too Many Attempts.') {
        alert('由於伺服器負載過重，請稍後再試');
      }
    }
  }

  // method
  public async Get(url: string, refReturn: TRef|null = null, sameOrgin = true, haveAuth = false) {
    return axios.get(sameOrgin ? this.headerPrefix + url : url, {
      headers: {
        Authorization: haveAuth ? this.authToken : false,
        Accept: this.acceptHeader,
        'Content-Type': this.contentType,
      },
    }).then((res) => {
        if (refReturn !== null) {
          refReturn.value = res.data;
        }
        return res.data;
      }).catch((err) => { this.ErrHdl(err); });
  }

  public async Post(url: string, dataset: any, refReturn: TRef|null = null, sameOrgin = true, haveAuth = false) {
    return axios.post(sameOrgin ? this.headerPrefix + url : url, dataset, {
      headers: {
        Authorization: haveAuth ? this.authToken : false,
        Accept: this.acceptHeader,
        'Content-Type': this.contentType,
      },
    })
      .then((res) => {
        if (refReturn !== null) {
          refReturn.value = res.data;
        }
        return res.data;
      })
      .catch((err) => { this.ErrHdl(err); });
  }

  public async Patch(url: string, dataset: any, refReturn: TRef|null = null, sameOrgin = true, haveAuth = false, hasFile = false) {
    return axios.patch(sameOrgin ? this.headerPrefix + url : url, dataset, {
      headers: {
        Authorization: haveAuth ? this.authToken : false,
        Accept: this.acceptHeader,
        'Content-Type': hasFile ? 'multipart/form-data' : this.contentType,
      },
    })
      .then((res) => {
        if (refReturn !== null) {
          refReturn.value = res.data;
        }
        return res.data;
      })
      .catch((err) => { this.ErrHdl(err); });
  }

  public async Delete(url: string, refReturn: any = null, sameOrgin = true, haveAuth = false) {
    return axios.delete(sameOrgin ? this.headerPrefix + url : url, {
      headers: {
        Authorization: haveAuth ? this.authToken : false,
        Accept: this.acceptHeader,
        'Content-Type': this.contentType,
      },
    })
      .then((res) => {
        if (refReturn !== null) {
          refReturn.value = res.data;
        }
        return res.data;
      })
      .catch((err) => { this.ErrHdl(err); });
  }
}
