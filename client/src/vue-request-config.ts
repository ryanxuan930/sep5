export default class VueRequestConfig {
  protected prefix = 'https://sports.nsysu.edu.tw/monkeyserver/api/';

  protected authToken = `Bearer ${localStorage.monkeyIdToken}`;

  protected acceptHeader = 'application/json';

  protected contentType = 'application/json; charset=utf-8';
}
