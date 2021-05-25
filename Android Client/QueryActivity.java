package com.marcgq.coursemanager;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.graphics.Color;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;
import android.widget.Toast;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.ProtocolException;
import java.net.URL;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.concurrent.ExecutionException;

public class QueryActivity extends AppCompatActivity {
    private TableLayout table;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_query);
        Bundle bundle = getIntent().getExtras();
        String name = bundle.getString("name");
        String ipAddress = bundle.getString("ipAddress");
        Button logoutButton = (Button) findViewById(R.id.logoutButton);
        Button sendButton = (Button) findViewById(R.id.sendButton);
        logoutButton.setOnClickListener(new View.OnClickListener() {
            public void onClick(View v) {
                finish();
            }
        });
        EditText edtTextQuery = (EditText) findViewById(R.id.editTextQuery);
        sendButton.setOnClickListener(new View.OnClickListener() {
            public void onClick(View v) {
                String query = edtTextQuery.getText().toString();
                Log.i("info", query);
                try {
                    new QueryActivity.GetQueryContent().execute(ipAddress+"/?"+query).get();
                } catch (ExecutionException | InterruptedException e) {
                    e.printStackTrace();
                }
            }
        });
        TextView nameTextView = (TextView) findViewById(R.id.nameTextView);
        nameTextView.setText("Welcome "+name);
        table = (TableLayout) findViewById(R.id.table);


    }
    private class GetQueryContent extends AsyncTask<String, Integer, String> {
        protected String doInBackground(String... urls) {
            URL url = null;
            try {
                url = new URL(urls[0]);
            } catch (MalformedURLException e) {
                e.printStackTrace();
            }
            HttpURLConnection connection = null;
            try {
                connection = (HttpURLConnection) url.openConnection();
            } catch (IOException e) {
                e.printStackTrace();
            }
            try {
                connection.setRequestMethod("GET");
            } catch (ProtocolException e) {
                e.printStackTrace();
            }
            connection.setDoOutput(true);
            connection.setConnectTimeout(5000);
            connection.setReadTimeout(5000);
            try {
                connection.connect();
            } catch (IOException e) {
                e.printStackTrace();
            }
            BufferedReader rd = null;
            try {
                rd = new BufferedReader(new InputStreamReader(connection.getInputStream()));
            } catch (IOException e) {
                e.printStackTrace();
            }
            String content = "", line = "";
            if (rd==null) return null;
            while (true) {
                try {
                    if (!((line = rd.readLine()) != null)) break;
                } catch (IOException e) {
                    e.printStackTrace();
                }
                content += line + "\n";
            }
            return content;
        }

        protected void onProgressUpdate(Integer... progress) {
        }

        protected void onPostExecute(String result) {
            // this is executed on the main thread after the process is over
            // update your UI here
            //Log.i("info", result);
            int i = 0, j =0;
            String nom = "";
            JSONArray json = null;
            ArrayList keys = new ArrayList<String>();
            if (result!=null) {
                try {
                    json = new JSONArray(result);
                } catch (JSONException e) {
                    e.printStackTrace();
                }

                try {

                        JSONObject res = json.getJSONObject(0);

                        Iterator iterator = res.keys();
                        while (iterator.hasNext()) {
                            keys.add((String) iterator.next());
                        }

                        ArrayList dataTable = new ArrayList();
                        for (Object k : keys) {
                            ArrayList key = new ArrayList<String>();
                            for (i = 0; i < json.length(); i++) {
                                key.add(i, json.getJSONObject(i).getString((String) k));
                            }
                            dataTable.add(j, key);
                            j++;
                        }

                        TableLayout table = (TableLayout) findViewById(R.id.table);


                        table.removeAllViews();
                        TableRow row = new TableRow(QueryActivity.this);
                        for (j = 0; j < keys.size(); j++) {
                            TextView text = new TextView(QueryActivity.this);
                            text.setWidth(250);
                            text.setText((String) keys.get(j));
                            row.addView(text);

                            text.setTextSize(20);
                        }
                        table.addView(row);
                        for (i = 0; i < ((ArrayList<?>) dataTable.get(0)).size(); i++) {
                            row = new TableRow(QueryActivity.this);
                            row.setPadding(3, 5, 3, 5);
                            for (j = 0; j < keys.size(); j++) {
                                TextView text = new TextView(QueryActivity.this);
                                text.setText(((ArrayList<?>) dataTable.get(j)).get(i).toString());

                                row.addView(text);

                            }
                            table.addView(row);
                        }


                } catch (JSONException e) {
                    e.printStackTrace();
                }
            } else {
                Toast.makeText(QueryActivity.this, "Unknown query, try again", Toast.LENGTH_SHORT).show();
            }
        }
    }
}