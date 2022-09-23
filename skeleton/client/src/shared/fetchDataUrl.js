/**
 * Return fetched data as DataURL.
 */
export default async url => {
  const res = await fetch(url);
  if (!res.ok) {
    console.warn(`${url} could not be loaded, response status is ${res.status}`);
    return null;
  }
  const blob = await res.blob();
  return new Promise(rslv => {
    const reader = new FileReader();
    reader.onload = () => rslv(reader.result);
    reader.readAsDataURL(blob);
  });
}