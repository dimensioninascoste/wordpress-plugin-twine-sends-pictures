Crea endpoint custom per ricevere le foto dei giocatori

              fetch('https://nomesito.com/wp-json/wp/v2/media/', {
                method: 'POST',
                headers: {
                  'Authorization': `Bearer ${authToken}`
                },
                body: formData
              })
