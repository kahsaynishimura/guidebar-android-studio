package br.com.guidebar.activities;
 
import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.SearchManager;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.res.Configuration;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.app.ActionBarDrawerToggle;
import android.support.v4.view.GravityCompat;
import android.support.v4.widget.DrawerLayout;
import android.util.Log;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.SearchView;
import android.widget.TextView;
import android.widget.Toast;

import com.facebook.Session;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.text.Normalizer;
import java.util.ArrayList;
import java.util.Locale;

import br.com.guidebar.R;
import br.com.guidebar.bean.Evento;
import br.com.guidebar.bean.Usuario;
import br.com.guidebar.classes.ConexaoHttpClient;
import br.com.guidebar.classes.ConnectionDetector;
import br.com.guidebar.classes.Guidebar;
import br.com.guidebar.classes.SessionManager;
import br.com.guidebar.customviews.CustomAdapterEvent;
import br.com.guidebar.customviews.DrawerArrayAdapter;

public class ListEventsActivity extends Activity {
    boolean clicou = false;
    private DrawerLayout mDrawerLayout;
    private ListView mDrawerList;
    private ActionBarDrawerToggle mDrawerToggle;
    private CharSequence mDrawerTitle;
    private CharSequence mTitle;
    private String[] mOptionTitles;
    private Integer REQUEST_CATEGORY = 1;
    private Integer REQUEST_FILTERS = 2;
    private ListEventTask mListEventTask = null;
    private SessionManager session;
    private ListView mEventList;
    private String selectedUrl;
    private String idCategoria = "0";
    private String idCidade = "0";
    private String idEstado = "0";
    private String isOpenBar = "0";
    private String dataInicio = "";
    private ArrayList<Evento> listaTotal = new ArrayList<Evento>();

    @Override
    protected void onNewIntent(Intent intent) {
        handleIntent(intent);
    }

    public String limparNome(String nome) {
        nome = nome.replaceAll(" ", "_");
        nome = Normalizer.normalize(nome, Normalizer.Form.NFD);
        nome = nome.replaceAll("[^\\p{ASCII}]", "");
        return nome;
    }

    private void handleIntent(Intent intent) {

        if (Intent.ACTION_SEARCH.equals(intent.getAction())) {
            String query = intent.getStringExtra(SearchManager.QUERY);
            ArrayList<Evento> listaEventos = new ArrayList<Evento>();
            for (Evento item : listaTotal) {
                String nome = limparNome((item.getNome())
                        .toLowerCase(Locale.ENGLISH));
                if (nome.contains(limparNome(query.toLowerCase(Locale.ENGLISH)))) {
                    listaEventos.add(item);
                }
            }
            ArrayAdapter<Evento> aa = new CustomAdapterEvent(
                    ListEventsActivity.this, R.layout.event_list_item,
                    listaEventos);
            mEventList.setAdapter(aa);
        } else {
            search();
        }
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_list_events);

        loadNavigationDrawer(savedInstanceState);
        handleIntent(getIntent());
        session = new SessionManager(getApplicationContext());
        session.checkLogin();

        selectedUrl = "events/index.json";
        setTitle("Eventos");

        Bundle parametros = getIntent().getExtras();
        if (parametros != null) {
            if (parametros.getString("url") != null) {
                selectedUrl = parametros.getString("url");
                if (selectedUrl.contains("events/myEvents.json?")) {
                    setTitle("Promovidos por mim");
                    mDrawerList.setItemChecked(1, true);
                } else if (selectedUrl.contains("bookmarks/myFavorites.json")) {
                    setTitle("Favoritos");
                    mDrawerList.setItemChecked(2, true);
                } else if (selectedUrl.contains("events/index.json?")) {
                    setTitle("Eventos");
                    mDrawerList.setItemChecked(3, true);
                }
            }
            if (parametros.getString("id_categoria") != null) {
                idCategoria = parametros.getString("category_id");
            }
            if (parametros.getString("id_estado") != null) {
                idEstado = parametros.getString("state_id");
            }
            if (parametros.getString("id_cidade") != null) {
                idCidade = parametros.getString("city_id");
            }
            if (parametros.getString("is_open_bar") != null) {
                isOpenBar = parametros.getString("is_open_bar");
            }
            if (parametros.getString("data_inicio") != null) {
                dataInicio = parametros.getString("start_date");
            }
            if (parametros.getString("nome_categoria") != null) {
                ListEventsActivity.this.setTitle("Categoria: "
                        + parametros.getString("nome_categoria"));
            }
        }
        mEventList = (ListView) findViewById(R.id.list);

    }

    @SuppressLint("NewApi")
    private void loadNavigationDrawer(Bundle savedInstanceState) {
        mTitle = mDrawerTitle = getTitle();
        mOptionTitles = getResources().getStringArray(R.array.options_array);
        mDrawerLayout = (DrawerLayout) findViewById(R.id.drawer_layout);
        mDrawerList = (ListView) findViewById(R.id.left_drawer);

        // set a custom shadow that overlays the main content when the drawer
        // opens
        mDrawerLayout.setDrawerShadow(R.drawable.drawer_shadow,
                GravityCompat.START);
        mDrawerList.setBackgroundColor(getResources().getColor(
                R.color.guidebar_background));

        // set up the drawer's list view with items and click listener
        mDrawerList.setAdapter(new DrawerArrayAdapter(this,
                R.layout.drawer_list_item, mOptionTitles));
        mDrawerList.setOnItemClickListener(new DrawerItemClickListener());

        // enable ActionBar app icon to behave as action to toggle nav drawer
        getActionBar().setDisplayHomeAsUpEnabled(true);
        getActionBar().setHomeButtonEnabled(true);

        // ActionBarDrawerToggle ties together the the proper interactions
        // between the sliding drawer and the action bar app icon
        mDrawerToggle = new ActionBarDrawerToggle(this, /* host Activity */
                mDrawerLayout, /* DrawerLayout object */
                R.drawable.ic_drawer, /* nav drawer image to replace 'Up' caret */
                R.string.action_drawer_open, /*
                                     * "open drawer" description for
									 * accessibility
									 */
                R.string.action_drawer_close /*
									 * "close drawer" description for
									 * accessibility
									 */
        ) {
            public void onDrawerClosed(View view) {
                getActionBar().setTitle(mTitle);
                invalidateOptionsMenu(); // creates call to
                // onPrepareOptionsMenu()
            }

            public void onDrawerOpened(View drawerView) {
                getActionBar().setTitle(mDrawerTitle);
                invalidateOptionsMenu(); // creates call to
                // onPrepareOptionsMenu()
            }
        };
        mDrawerLayout.setDrawerListener(mDrawerToggle);

    }

    /* Called whenever we call invalidateOptionsMenu() */
    @Override
    public boolean onPrepareOptionsMenu(Menu menu) {
        // If the nav drawer is open, hide action items related to the content
        // view
        boolean drawerOpen = mDrawerLayout.isDrawerOpen(mDrawerList);
        menu.findItem(R.id.search).setVisible(!drawerOpen);
        return super.onPrepareOptionsMenu(menu);
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        MenuInflater inflater = getMenuInflater();
        inflater.inflate(R.menu.options_menu, menu);
        // Associate searchable configuration with the SearchView
        SearchManager searchManager = (SearchManager) getSystemService(Context.SEARCH_SERVICE);
        SearchView searchView = (SearchView) menu.findItem(R.id.search)
                .getActionView();
        searchView.setSearchableInfo(searchManager
                .getSearchableInfo(getComponentName()));

        return true;

    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // The action bar home/up action should open or close the drawer.
        // ActionBarDrawerToggle will take care of this.
        if (mDrawerToggle.onOptionsItemSelected(item)) {
            return true;
        }
        // Handle action buttons
        switch (item.getItemId()) {
            case R.id.search:

                return true;
            default:
                return super.onOptionsItemSelected(item);
        }
    } /* The click listner for ListView in the navigation drawer */

    private void selectItem(int position) {

        Intent i;
        switch (position) {
            case 0:
                if (!clicou) {
                    clicou = true;
                    i = new Intent(ListEventsActivity.this,
                            ListEventsActivity.class);
                    selectedUrl = "events/index.json";
                    mTitle = "Todos os eventos";
                    startActivity(i);
                    break;
                }

            case 1:
                if (!clicou) {
                    clicou = true;
                    i = new Intent(ListEventsActivity.this,
                            ListEventsActivity.class);
                    selectedUrl = "events/myEvents.json";
                    mTitle = "Promovidos por mim";
                    startActivity(i);
                    break;
                }
            case 2:
                if (!clicou) {
                    clicou = true;
                    i = new Intent(ListEventsActivity.this,
                            ListEventsActivity.class);
                    selectedUrl = "bookmarks/myFavorites.json";

                    mTitle = "Favoritos";
                    startActivity(i);
                    break;
                }
            case 3:
                if (!clicou) {
                    clicou = true;
                    mTitle = "Resultado da busca";
                    startActivityForResult(new Intent(ListEventsActivity.this,
                            FiltersActivity.class), REQUEST_FILTERS);
                    break;
                }
            case 4:
                if (!clicou) {
                    clicou = true;
                    startActivity(new Intent(ListEventsActivity.this,
                            ListPurchasesActivity.class));
                    break;
                }
            case 5:
                if (!clicou) {
                    clicou = true;
                    mTitle = "Busca por categoria";
                    startActivityForResult(new Intent(ListEventsActivity.this,
                            ListCategoriesActivity.class), REQUEST_CATEGORY);
                    break;
                }
            case 6:
                if (!clicou) {
                    clicou = true;
                    i = new Intent(ListEventsActivity.this, ViewUserActivity.class);
                    i.putExtra("id",
                            session.getUserDetails().get(SessionManager.KEY_ID));
                    startActivity(i);
                    break;
                }
            case 7:
                if (!clicou) {
                    clicou = true;
                    AlertDialog.Builder confirmLogOut = new AlertDialog.Builder(
                            this);
                    confirmLogOut.setTitle("Sair do aplicativo");
                    confirmLogOut
                            .setMessage("Deseja realmente sair do aplicativo?");
                    confirmLogOut.setPositiveButton(getString(R.string.yes),
                            new DialogInterface.OnClickListener() {

                                @Override
                                public void onClick(DialogInterface dialog,
                                                    int which) {
                                    logout();
                                }
                            });

                    confirmLogOut.setNegativeButton(getString(R.string.no),
                            new DialogInterface.OnClickListener() {

                                @Override
                                public void onClick(DialogInterface dialog,
                                                    int which) {
                                    // dialog.cancel();
                                }
                            });
                    confirmLogOut.show();

                }
                clicou = false;
                break;
            // case 9:
            // if (!clicou) {
            // clicou = true;
            // i = new Intent(this, NewEventActivity.class);
            // i.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
            // startActivityForResult(i, 0);
            // break;
            // }
            default:
                break;

        }
        if (position != 4 && position != 5 && position != 7) {
            mDrawerList.setItemChecked(position, true);
            // setTitle(mOptionTitles[position]);
        } else {
            mDrawerList.setItemChecked(position, false);
        }

        mDrawerLayout.closeDrawer(mDrawerList);
    }

    @Override
    public void setTitle(CharSequence title) {
        mTitle = title;
        getActionBar().setTitle(mTitle);
    }

    /**
     * When using the ActionBarDrawerToggle, you must call it during
     * onPostCreate() and onConfigurationChanged()...
     */

    @Override
    protected void onPostCreate(Bundle savedInstanceState) {
        super.onPostCreate(savedInstanceState);
        // Sync the toggle state after onRestoreInstanceState has occurred.
        mDrawerToggle.syncState();

    }

    @Override
    protected void onResume() {
        super.onResume();
        clicou = false;

    }

    @Override
    public void onConfigurationChanged(Configuration newConfig) {
        super.onConfigurationChanged(newConfig);
        // Pass any configuration change to the drawer toggls
        mDrawerToggle.onConfigurationChanged(newConfig);
    }

    protected void search() {
        ConnectionDetector cd = new ConnectionDetector(getApplicationContext());
        Boolean isInternetPresent = cd.isConnectingToInternet();
        if (isInternetPresent) {
            mListEventTask = new ListEventTask();
            mListEventTask.execute((Void) null);
        } else {
            Toast.makeText(ListEventsActivity.this,
                    R.string.error_network_connection, Toast.LENGTH_SHORT)
                    .show();
        }

    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        if (resultCode == RESULT_OK) {
            if (requestCode == REQUEST_CATEGORY) {
                idCategoria = data.getStringExtra("category_id");
                selectedUrl = "events/index.json?categoryFilter=1";
            } else if (requestCode == REQUEST_FILTERS) {
                idEstado = data.getStringExtra("state_id");
                idCidade = data.getStringExtra("city_id");
                isOpenBar = data.getStringExtra("is_open_bar");
                idCategoria = data.getStringExtra("category_id");
                dataInicio = data.getStringExtra("start_date");
                selectedUrl = "events/index.json?";
            }
        }
        search();
    }

    public void logout() {
        session.logoutUser();
        Session.getActiveSession().closeAndClearTokenInformation();
        finish();
    }

    private class DrawerItemClickListener implements
            ListView.OnItemClickListener {
        @Override
        public void onItemClick(AdapterView<?> parent, View view, int position,
                                long id) {
            selectItem(position);
        }
    }

    class ListEventTask extends AsyncTask<Void, Void, ArrayList<Evento>> {

        // JSON Node names
        private static final String TAG_ID = "id";
        private static final String TAG_NOME = "name";
        private static final String TAG_DESCRICAO = "description";
        private static final String TAG_ID_PROMOTOR = "user_id";
        private static final String TAG_DATA_INICIO = "start_date";
        private static final String TAG_AVALIACAO_MEDIA = "average_rating";
        private static final String TAG_FILENAME = "thumb";
        private static final String TAG_EVENTOS = "events";
        private static final String TAG_EVENTO = "Event";
        JSONArray array = null;

        @Override
        protected ArrayList<Evento> doInBackground(Void... params) {
            ArrayList<Evento> listaEventos = new ArrayList<Evento>();
            ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
            parametrosPost.add(new BasicNameValuePair("email", session
                    .getUserDetails().get(SessionManager.KEY_EMAIL)));
            parametrosPost.add(new BasicNameValuePair("token", session
                    .getUserDetails().get(SessionManager.KEY_ACCESS_TOKEN)));

            parametrosPost.add(new BasicNameValuePair(
                    "data[Event][category_id]", idCategoria));
            parametrosPost.add(new BasicNameValuePair("data[Event][city_id]",
                    idCidade));
            parametrosPost.add(new BasicNameValuePair("data[Event][state_id]",
                    idEstado));
            parametrosPost.add(new BasicNameValuePair(
                    "data[Event][is_open_bar]", isOpenBar));
            parametrosPost.add(new BasicNameValuePair(
                    "data[Event][start_date]", dataInicio));

            String url = Guidebar.serverUrl + "" + selectedUrl;

            String respostaRetornada = "";
            try {
                if ("events/index.json" == selectedUrl) {
                    respostaRetornada = ConexaoHttpClient.executaHttpGet(url);
                } else {
                    respostaRetornada = ConexaoHttpClient.executaHttpPost(url,
                            parametrosPost);
                }
                String resposta = respostaRetornada.toString();

                if (!resposta.replaceAll("\\s+", "").equals("[]")) {
                    JSONObject json = new JSONObject(resposta);
                    try {
                        array = json.getJSONArray(TAG_EVENTOS);
                        for (int i = 0; i < array.length(); i++) {
                            JSONObject event = array.getJSONObject(i);
                            JSONObject c = event.getJSONObject(TAG_EVENTO);

                            Evento obj1 = new Evento();

                            obj1.setNome(c.getString(TAG_NOME));
                            obj1.setDescricao(c.getString(TAG_DESCRICAO));
                            obj1.setPromotor(new Usuario(Integer.parseInt(c
                                    .getString(TAG_ID_PROMOTOR))));
                            obj1.setDataInicio(c.getString(TAG_DATA_INICIO));
                            obj1.setAvaliacaoMedia(Float.parseFloat(c
                                    .getString(TAG_AVALIACAO_MEDIA)));
                            obj1.setFilename(c.getString(TAG_FILENAME).toString());
                            obj1.setId(Integer.parseInt(c.getString(TAG_ID)));
                            listaEventos.add(obj1);
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }
            } catch (Exception erro) {
                Log.i("erro", "erro = " + erro);
            }

            return listaEventos;
        }

        @Override
        protected void onPostExecute(final ArrayList<Evento> listaEventos) {
            mListEventTask = null;
            listaTotal = listaEventos;
            ArrayAdapter<Evento> aa = new CustomAdapterEvent(
                    ListEventsActivity.this, R.layout.event_list_item,
                    listaEventos);
            TextView txtNoEvents = (TextView) findViewById(R.id.txtNoEvents);
            if (listaEventos.isEmpty()) {
                txtNoEvents.setText(getString(R.string.error_no_parties));
                txtNoEvents.setTextColor(getResources().getColor(
                        R.color.guidebar_background));
                txtNoEvents.setVisibility(View.VISIBLE);
            } else {
                txtNoEvents.setVisibility(View.GONE);
            }
            mEventList.setAdapter(aa);

        }

        @Override
        protected void onCancelled() {
            mListEventTask = null;
        }
    }

}
