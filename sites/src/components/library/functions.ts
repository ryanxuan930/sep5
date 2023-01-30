export function getUrlParams(url: string, params:string|string[]) {
  try {
    var urlParams = new URL(url).searchParams;
  } catch (err) {
    return null;
  }
  if (Array.isArray(params)) {
    const data:(string|null)[] = [];
    params.forEach(param => { data.push(urlParams.get(param)) });
    return data;
  } else {
    console.log(url, urlParams.get(params))
    return urlParams.get(params);
  }
}

export function paginationText(page: string|number) {
  if (!isNaN(Number(page))) {
    return page;
  } else if (page === 'pagination.previous') {
    return '❮';
  } else {
    return '❯';
  }
}